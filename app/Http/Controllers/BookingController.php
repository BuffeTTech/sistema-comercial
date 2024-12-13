<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\BuffetStatus;
use App\Enums\DayWeek;
use App\Enums\DecorationStatus;
use App\Enums\FoodStatus;
use App\Enums\GuestStatus;
use App\Enums\ScheduleStatus;
use App\Events\BookingCreatedEvent;
use App\Events\BookingUpdatedEvent;
use App\Events\ChangeBookingStatusEvent;
use App\Http\Requests\Bookings\StoreBookingRequest;
use App\Http\Requests\Bookings\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Buffet;
use App\Models\Configuration;
use App\Models\Decoration;
use App\Models\Food;
use App\Models\Guest;
use App\Models\Recommendation;
use App\Models\Schedule;
use Carbon\Carbon;
use DateTime;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BookingController extends Controller
{
    protected Hashids $hashids;

    public function __construct(
        protected Buffet $buffet,
        protected Schedule $schedule,
        protected Booking $booking,
        protected Food $food,
        protected Decoration $decoration,
        protected Guest $guest,
        protected Recommendation $recommendation,
        protected Configuration $configuration
    )
    {
        $this->hashids = new Hashids(config('app.name'));
    }


    private static int $min_days = 30;
    private static int $max_days_update_booking = 10;

    private function current_party(Buffet $buffet){
        // Lista de somente as próximas reservas 
        $bookings = $this->booking
            ->with(['schedule'=>function ($query) {
                $query->orderBy('start_time', 'asc');
            }, 'food','decoration', 'user'])
            ->where('status', BookingStatus::APPROVED->name)
            // ->where('buffet_id', $buffet->id)
            ->where('party_day', '>=', date('Y-m-d'))
            ->get();

        $dataAgora = Carbon::now()->locale('pt-BR');

        $current_party = null;

        foreach($bookings as $key => $booking)
        {
            $schedule = $this->schedule->where('id',$booking->schedule_id)->get()->first();
            $booking_start = Carbon::parse($booking->party_day . ' ' . $schedule->start_time);
            $booking_end = Carbon::parse($booking->party_day . ' ' . $schedule->start_time);
            $booking_end->addMinutes($schedule->duration);

            if($dataAgora < $booking_end && $dataAgora > $booking_start){
                $current_party = $booking;
                break;
            }
        }

        return $current_party;
    }

    private function guest_counter(Booking $party, Request $request){
        
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();


        $unblocked_guests = $this->guest
            ->where('booking_id',$party->id)
            ->where('buffet_id', $buffet->id)
            ->where('status','!=', GuestStatus::BLOCKED->name)
            ->get();

        $present_guests  = $this->guest
            ->where('booking_id',$party->id)
            ->where('buffet_id', $buffet->id)
            ->where('status',GuestStatus::PRESENT->name)
            ->get();

        $extra_guests = $this->guest
        ->where('booking_id',$party->id)
        ->where('buffet_id', $buffet->id)
        ->where('status',GuestStatus::EXTRA->name)
        ->get();

        $guest_counter = ['present'=>$present_guests->count(), 'unblocked'=>$unblocked_guests->count(), 'extras' =>$extra_guests->count()];

        return $guest_counter;
    }

    /**
     * Display a listing of the resource.
     */

    public function list(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();;
        }
        $configuration = $this->configuration->where('buffet_id', $buffet->id)->first();
        $max_days_update_booking = self::$max_days_update_booking;
        if($configuration) {
            $min_days = $configuration->max_days_update_booking;
        }

        $format = $request->get('format', 'all');
        $status = BookingStatus::PENDENT->name; 
        if($format == 'pendent') {
            $bookings = $this->booking->with(['schedule'=>function ($query) {
                $query->orderBy('start_time', 'asc');
            }, 'food','decoration', 'user'])->where('status', $status)->where('buffet_id', $buffet->id)->where('party_day', '>=', date('Y-m-d'))->orderBy('party_day', 'asc')->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
            $this->authorize('viewPendentBookings', [Booking::class, $buffet]);
        } else {
            $format = 'all';
            $bookings =  $this->booking->where('buffet_id', $buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
            $this->authorize('viewAllBookings', [Booking::class, $buffet]);
        }

        return view('bookings.list', ['bookings'=>$bookings,'buffet' => $buffet, 'max_days_update_booking'=>$max_days_update_booking, 'format'=>$format]);
    }
    
     
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        // Lista de somente as próximas reservas 
        $bookings = $this->booking->with(['schedule'=>function ($query) {
            $query->orderBy('start_time', 'asc');
        }, 'food','decoration', 'user'])->where('status', BookingStatus::APPROVED->name)->where('buffet_id', $buffet->id)->where('party_day', '>=', date('Y-m-d'))->orderBy('party_day', 'asc')->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));

        $this->authorize('viewNextBookings', [Booking::class, $buffet]);
        $current_party = $this->current_party($buffet);

        return view('bookings.index', ['bookings'=>$bookings,'buffet' => $buffet, 'current_party'=>$current_party]);
    }

    public function my_bookings(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        // Lista de somente as próximas reservas 
        $bookings = $this->booking
                        ->with(['schedule'=>function ($query) {
                            $query->orderBy('start_time', 'asc');
                        }, 'food','decoration', 'user'])
                        ->where('user_id', auth()->user()->id)
                        ->orderBy('party_day', 'asc')
                        ->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));

        $this->authorize('listMyBookings', [Booking::class, $buffet]);

        return view('bookings.my-bookings', ['bookings'=>$bookings,'buffet' => $buffet]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        $this->authorize('create', [Booking::class, $buffet]);

        $configuration = $this->configuration->where('buffet_id', $buffet->id)->first();
        $min_days = self::$min_days;
        if($configuration) {
            $min_days = $configuration->min_days_booking;
        }

        $foods = $this->food->where('buffet_id', $buffet->id)->where('status', FoodStatus::ACTIVE->name)->get();
        $decorations = $this->decoration->where('buffet_id', $buffet->id)->where('status', DecorationStatus::ACTIVE->name)->get();

        return view('bookings.create', ['buffet'=>$buffet, 'foods'=>$foods, 'decorations'=>$decorations, 'min_days'=>$min_days, 'configuration'=>$configuration])->with(['success'=>'Reserva criada com sucesso!']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        $this->authorize('create', [Booking::class, $buffet]);

        dd($request);

        $configuration = $this->configuration->where('buffet_id', $buffet->id)->first();
        $min_days = self::$min_days;
        if($configuration) {
            $min_days = $configuration->min_days_booking;
        }
        
        // valida a hora
        $schedule = $this->schedule->where('id', $request->schedule_id)->where('buffet_id', $buffet->id)->get()->first();
        
        if(!$schedule) {
            return redirect()->back()->withErrors(['schedule_id'=>'Schedule not found'])->withInput();
        }
        
        if($schedule->status !== ScheduleStatus::ACTIVE->name) {
            return redirect()->back()->withErrors(['schedule_id'=>'Schedule is not active'])->withInput();
        }
        
        // valida o dia
        $party_day = $request->party_day;
        
        $today_date = date('Y-m-d');
        $party_start = date("Y-m-d H:i",strtotime(Carbon::parse($party_day)->setHours($schedule['start_time'])));
        $party_end = date("Y-m-d H:i",strtotime(Carbon::parse($party_day)->setHours($schedule['start_time'])->addMinutes($schedule['duration'])));
        $max_date = Carbon::parse($today_date)->addDays($min_days);

        if($max_date > $party_start) {
            return redirect()->back()->withErrors(['party_day'=>"Party should be scheduled with a minimum of ".$min_days." days"])->withInput();
        }
        if($party_end < $party_start) {
            return redirect()->back()->withErrors(['party_day'=>"Party can not end before start"])->withInput();
        }
        
        // Valida se existe alguma reserva neste horário
        $booking_exists_in_time = $this->booking
        ->where('buffet_id', $buffet->id)
        ->where('party_day', $party_day)
        ->where('schedule_id', $schedule->id)
        ->where('status', BookingStatus::APPROVED->name)
        ->get()->first();
        
        
        if($booking_exists_in_time) {
            return redirect()->back()->withErrors(['party_day'=>"Booking already exists in this time"])->withInput();
        }
        
        // Valida o pacote de comida
        $food = $this->food
        ->where('slug', $request->food_id)
        ->where('buffet_id', $buffet->id)
        ->where('status', FoodStatus::ACTIVE->name)
        ->get()
        ->first();
        
        if(!$food) {
            return redirect()->back()->withErrors(['food_id'=>"Food not found"])->withInput();
        }
        
        // Valida o pacote de decorações
        $decoration = $this->decoration
            ->where('slug', $request->decoration_id)
            ->where('buffet_id', $buffet->id)
            ->where('status', DecorationStatus::ACTIVE->name)
            ->get()
            ->first();
            
        if(!$decoration) {
            return redirect()->back()->withErrors(['decoration_id'=>"Decoration not found"])->withInput();
        }
            
        // Cria a reserva
        $booking = $this->booking->create([
            'name_birthdayperson'=>$request->name_birthdayperson,
            'years_birthdayperson'=>$request->years_birthdayperson,
            'num_guests'=>$request->num_guests,
            'party_day'=>$party_day,
            'buffet_id'=>$buffet->id,
            'user_id'=>auth()->user()->id,
            'food_id'=>$food->id,
            'price_food'=>$food->price,
            'decoration_id'=>$decoration->id,
            'price_decoration'=>$decoration->price,
            'schedule_id'=>$schedule->id,
            'price_schedule'=>0,
            'discount'=>0,
            'status'=>BookingStatus::PENDENT->name
        ]);

        event(new BookingCreatedEvent($booking));

        return redirect()->back()->with(['success'=>'Reserva criada com sucesso! Fique atento em seu e-mail.']);
        // return redirect()->route('booking.show', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id])->with(['success'=>'Recomendação criada com sucesso! Fique atento em seu e-mail.']);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        $recommendations = $this->recommendation->where('buffet_id',$buffet->id)->get();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        $booking_id = $this->hashids->decode($request->booking);
        if(!$booking_id) {
            return redirect()->back()->withErrors(['message'=>'Reserva não encontrada'])->withInput();
        }
        
        $booking_id = $booking_id[0];

        $booking = $this->booking
            ->where('id',$booking_id)
            ->where('buffet_id', $buffet->id)
            ->with(['food', 'decoration', 'schedule'])->get()->first();

        if(!$booking) {
            if(Redirect::back()->getTargetUrl() !== url()->current()) {
                return redirect()->back()->withErrors(['message'=>'Reserva não encontrada'])->withInput();
            } else {
                return redirect()->route('booking.index', ['buffet'=>$buffet->slug])->withErrors(['message'=>'Reserva não encontrada'])->withInput();
            }
        }

        $guests = $this->guest
            ->where('booking_id',$booking_id)
            ->where('buffet_id', $buffet->id)
            ->orderBy('name', 'asc')
            ->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));

        $guest_counter = $this->guest_counter($booking, $request);

        $this->authorize('view', [Booking::class, $booking, $buffet]);

        return view('bookings.show', ['buffet'=>$buffet,'booking'=>$booking, 'recommendations'=>$recommendations, 'guests'=>$guests, 'guest_counter' => $guest_counter]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        // $this->authorize('update', Booking::class);

        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['errors'=>'Buffet not found'])->withInput();
        }

        $booking_id = $this->hashids->decode($request->booking);
        if(!$booking_id) {
            return redirect()->back()->withErrors(['message'=>'Reserva não encontrada'])->withInput();
        }
        
        $booking_id = $booking_id[0];
        
        $booking = $this->booking->where('id', $booking_id)->where('buffet_id', $buffet->id)->get()->first();
        
        if(!$booking) {
            return redirect()->back()->withErrors(['errors'=>'Reserva não encontrada'])->withInput();
        }

        $this->authorize('update', [Booking::class, $booking, $buffet]);

        if($booking['status'] !== BookingStatus::APPROVED->name && $booking['status'] !== BookingStatus::PENDENT->name) {
            return redirect()->back()->withErrors(['errors'=>'Esta reserva não pode mais ser editada.'])->withInput();
        }

        $configuration = $this->configuration->where('buffet_id', $buffet->id)->first();
        $max_days_update_booking = self::$max_days_update_booking;
        if($configuration) {
            $max_days_update_booking = $configuration->max_days_update_booking;
        }

        $schedule = $this->schedule->where('id', $booking->schedule_id)->where('buffet_id', $buffet->id)->get()->first();
        if(!$schedule) {
            return redirect()->back()->withErrors(['schedule_id'=>'Horário não encontrado.'])->withInput();
        }

        $party_day = $request->party_day;

        $today_date = date('Y-m-d');
        $party_start = date("Y-m-d H:i",strtotime(Carbon::parse($party_day)->setHours($schedule['start_time'])));
        $max_date = Carbon::parse($today_date)->addDays($max_days_update_booking);

        if($max_date > $party_start) {
            return redirect()->back()->withErrors(['party_day'=>"Uma festa só pode ser agendada ou editada com ".$max_days_update_booking." dias de antecedencia."])->withInput();
        }

        $foods = $this->food->where('buffet_id', $buffet->id)->where('status', FoodStatus::ACTIVE->name)->get();
        $decorations = $this->decoration->where('buffet_id', $buffet->id)->where('status', DecorationStatus::ACTIVE->name)->get();

        return view('bookings.update', ['buffet'=>$buffet, 'foods'=>$foods, 'decorations'=>$decorations, 'booking'=>$booking]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $booking_id = $this->hashids->decode($request->booking);
        if(!$booking_id) {
            return redirect()->back()->withErrors(['message'=>'Reserva não encontrada'])->withInput();
        }
        
        $booking_id = $booking_id[0];

        // valida se o booking existe
        $booking = $this->booking->where('buffet_id', $buffet->id)->where('id', $booking_id)->get()->first();
        if(!$booking) {
            return redirect()->back()->withErrors(['errors'=>'Reserva não encontrada'])->withInput();
        }
        
        $this->authorize('update', [Booking::class, $booking, $buffet]);

        $configuration = $this->configuration->where('buffet_id', $buffet->id)->first();
        $max_days_update_booking = self::$max_days_update_booking;
        if($configuration) {
            $max_days_update_booking = $configuration->max_days_update_booking;
        }

        if($booking['status'] !== BookingStatus::APPROVED->name && $booking['status'] !== BookingStatus::PENDENT->name) {
            return redirect()->back()->withErrors(['errors'=>'Esta reserva não pode mais ser editada.'])->withInput();
        }

        // valida a hora
        $schedule = $this->schedule->where('id', $request->schedule_id)->where('buffet_id', $buffet->id)->get()->first();

        if(!$schedule) {
            return redirect()->back()->withErrors(['schedule_id'=>'Horário não encontrado.'])->withInput();
        }
        
        if($schedule->status !== ScheduleStatus::ACTIVE->name) {
            return redirect()->back()->withErrors(['schedule_id'=>'O horário escolhido não está mais ativo.'])->withInput();
        }

        // valida o dia
        $party_day = $request->party_day;

        $today_date = date('Y-m-d');
        $party_start = date("Y-m-d H:i",strtotime(Carbon::parse($party_day)->setHours($schedule['start_time'])));
        $party_end = date("Y-m-d H:i",strtotime(Carbon::parse($party_day)->setHours($schedule['start_time'])->addMinutes($schedule['duration'])));
        $max_date = Carbon::parse($today_date)->addDays($max_days_update_booking);

        if($max_date > $party_start) {
            return redirect()->back()->withErrors(['party_day'=>"Uma festa só pode ser agendada ou editada com ".$max_days_update_booking." dias de antecedencia."])->withInput();
        }
        if($party_end < $party_start) {
            return redirect()->back()->withErrors(['party_day'=>"Uma festa não pode finalizar antes de começar."])->withInput();
        }

        // Valida se existe alguma reserva neste horário
        $booking_exists_in_time = $this->booking
            ->where('buffet_id', $buffet->id)
            ->where('party_day', $party_day)
            ->where('schedule_id', $schedule->id)
            ->where('status', BookingStatus::APPROVED->name)
            ->get()->first();
        if($booking_exists_in_time && $booking_exists_in_time->id !== $booking->id) {
            return redirect()->back()->withErrors(['schedule_id'=>"Já existe uma festa neste horário."])->withInput();
        }

        // Valida o pacote de comida
        $food = $this->food
            ->where('slug', $request->food_id)
            ->where('buffet_id', $buffet->id)
            ->where('status', FoodStatus::ACTIVE->name)
            ->get()
            ->first();

        if(!$food) {
            return redirect()->back()->withErrors(['food_id'=>"Pacote de comida não encontrado"])->withInput();
        }

        // Valida o pacote de decorações
        $decoration = $this->decoration
            ->where('slug', $request->decoration_id)
            ->where('buffet_id', $buffet->id)
            ->where('status', DecorationStatus::ACTIVE->name)
            ->get()
            ->first();

        if(!$decoration) {
            return redirect()->back()->withErrors(['decoration_id'=>"Pacote de decoração não encontrado"])->withInput();
        }

        $old_booking = clone $booking;

        $booking->update([
            'name_birthdayperson'=>$request->name_birthdayperson,
            'years_birthdayperson'=>$request->years_birthdayperson,
            'num_guests'=>$request->num_guests,
            'party_day'=>$party_day,
            'buffet_id'=>$buffet->id,
            'user_id'=>auth()->user()->id,
            'food_id'=>$food->id,
            'price_food'=>$food->price,
            'decoration_id'=>$decoration->id,
            'price_decoration'=>$decoration->price,
            'schedule_id'=>$schedule->id,
            'price_schedule'=>0,
            'discount'=>0,
        ]);

        if($request->status) {
            $booking->update([
                'status'=>$request->status
            ]);
        }

        event(new BookingUpdatedEvent($old_booking, $booking->fresh()));

        return redirect()->route('booking.edit', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id])->with(['success'=>'Reserva atualizada com sucesso!']);
    }

    public function destroy(Request $request)
    {

    }

    public function change_status(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $booking_id = $this->hashids->decode($request->booking);
        if(!$booking_id) {
            return redirect()->back()->withErrors(['message'=>'Reserva não encontrada'])->withInput();
        }
        
        $booking_id = $booking_id[0];
        
        $booking = $this->booking->where('id',$booking_id)->with(['food', 'decoration', 'schedule'])->get()->first();

        if($request->status == BookingStatus::CANCELED->name) {
            $this->authorize('cancel', [Booking::class, $booking, $buffet]);
        } else {
            $this->authorize('change_status', [Booking::class, $booking, $buffet]);
        }

        $booking->update(['status'=>$request->status]);

        if($request->status === BookingStatus::APPROVED->name) {
            $booking_exists_in_time = $this->booking
                ->where('buffet_id', $buffet->id)
                ->where('party_day', $booking->party_day)
                ->where('schedule_id', $booking->schedule_id)
                ->where('status', '!=', BookingStatus::APPROVED->name)
                ->get();

            foreach ($booking_exists_in_time as $bk) {
                $bk->status = BookingStatus::REJECTED->name;
                $bk->save();
                event(new ChangeBookingStatusEvent($bk));
            }
                
        }

        event(new ChangeBookingStatusEvent($booking));

        return redirect()->back()->with(['success'=>'Reserva atualizada com sucesso!']);
    }

    public function calendar(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        return view('bookings.calendar', ['buffet'=>$buffet]);
    }

    public function buffet_calendar(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        return view('bookings.calendar-page', ['buffet'=>$buffet]);
    }

    public function reschedule_party() {
        
    }
    
    // API
    public function api_calendar(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return response()->json(['message' => 'Buffet not found'], 422);
        }

        $bookings = $this->booking
                        ->with(['schedule'])
                        ->where('buffet_id', $buffet->id)
                        ->where('status', '!=', BookingStatus::CANCELED->name)
                        ->where('status', '!=', BookingStatus::REJECTED->name)
                        ->where('status', '!=', BookingStatus::PENDENT->name)
                        ->get();
        return response()->json($bookings);
    }


    public function api_get_open_schedules_by_day_and_buffet(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return response()->json(['message' => 'Buffet not found'], 422);
        }

        $date = new DateTime($request->day);
        $dayOfWeek = strtoupper($date->format('l'));

        if(!DayWeek::is_in_name($dayOfWeek)) {
            return response()->json(['message' => 'Day not found'], 422);
        }

        if($request->booking) {
            $booking_id = $this->hashids->decode($request->booking);
            if(!$booking_id) {
                return redirect()->back()->withErrors(['message'=>'Reserva não encontrada'])->withInput();
            }
            
            $booking_id = $booking_id[0];

            $booking = $this->booking->where('id', $booking_id)->get()->first();
            if(!$booking) {
                return response()->json(['message' => 'Reserva não encontrada'], 422);
            }

            $booking_id = $booking->id;

            $schedules = $this->schedule
                ->leftJoin('bookings', function ($join) use ($date) {
                    $join->on('schedules.id', '=', 'bookings.schedule_id')
                        ->where('bookings.party_day', '=', $date);
                })
                ->where(function ($query) use ($booking_id) {
                    $query->whereNull('bookings.schedule_id')
                        ->orWhere('bookings.id', '=', $booking_id);
                })
                ->orderBy('schedules.start_time', 'asc')
                ->where('schedules.buffet_id', $buffet->id)
                ->where('schedules.day_week', $dayOfWeek)
                ->where('schedules.status', ScheduleStatus::ACTIVE->name)
                ->select('schedules.*')
                ->groupBy('schedules.id') // Agrupa pelo campo id
                ->get()
                ->map(function ($item) use ($booking_id) {
                    if ($item->id === $booking_id) {
                        $item->special_id = $item->id;
                    } else {
                        $item->special_id = null;
                    }
                    return $item;
                })
                ->toArray();
            return response()->json(['day'=>$date, 'day_week'=>$dayOfWeek, 'schedules'=>$schedules], 200);

        }

        $schedules = $this->schedule
                ->leftJoin('bookings', function ($join) use ($date) {
                    $join->on('schedules.id', '=', 'bookings.schedule_id')
                        ->where('bookings.party_day', '=', $date);
                })
                ->where(function ($query) {
                    $query->whereNull('bookings.schedule_id')
                        ->orWhereIn('bookings.status', [
                            BookingStatus::PENDENT->name,
                            BookingStatus::REJECTED->name,
                            BookingStatus::CANCELED->name,
                        ]);
                })
                ->orderBy('schedules.start_time', 'asc')
                ->where('schedules.status', ScheduleStatus::ACTIVE->name)
                ->where('schedules.buffet_id', $buffet->id)
                ->where('schedules.day_week', $dayOfWeek)
                ->select('schedules.*')
                ->groupBy('schedules.id') // Agrupa pelo campo id
                ->get();


        return response()->json(['day'=>$date, 'day_week'=>$dayOfWeek, 'schedules'=>$schedules], 200);
    }

    public function party_mode(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }
        $this->authorize('party_mode', [Booking::class, $buffet]);
        
        $current_party = $this->current_party($buffet);
        if(!$current_party) {
            // a propria blade tem um if pra validar se existe ou não
            return view('bookings.party_mode',['booking'=>$current_party,'buffet'=>$buffet]);
        }

        $guests = $this->guest
                       ->where('booking_id',$current_party->id)
                       ->where('buffet_id', $buffet->id)
                       ->orderBy('name', 'asc')
                       ->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));

        $guest_counter = $this->guest_counter($current_party, $request);

        return view('bookings.party_mode',['booking'=>$current_party,'buffet'=>$buffet, 'guests'=>$guests, 'guest_counter'=>$guest_counter]);
    }

    public function api_get_schedules_by_day(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return response()->json(['message' => 'Buffet not found'], 422);
        }

        $date = new DateTime($request->day);
        $dayOfWeek = strtoupper($date->format('l'));

        if(!DayWeek::is_in_name($dayOfWeek)) {
            return response()->json(['message' => 'Day not found'], 422);
        }

        $schedules = $this->schedule
                ->orderBy('schedules.start_time', 'asc')
                ->where('schedules.status', ScheduleStatus::ACTIVE->name)
                ->where('schedules.buffet_id', $buffet->id)
                ->where('schedules.day_week', $dayOfWeek)
                ->select('schedules.*')
                ->groupBy('schedules.id') // Agrupa pelo campo id
                ->get();


        return response()->json(['day'=>$date, 'day_week'=>$dayOfWeek, 'schedules'=>$schedules], 200);

    }

    public function api_get_disponibility_by_day(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
    
        if (!$buffet || !$buffet_slug) {
            return response()->json(['message' => 'Buffet not found'], 422);
        }
    
        // Data e dia da semana
        $date = new DateTime($request->day);
        $dayOfWeek = strtoupper($date->format('l')); // Dia da semana em texto (ex: THURSDAY)
    
        if (!DayWeek::is_in_name($dayOfWeek)) {
            return response()->json(['message' => 'Day not found'], 422);
        }
    
        // Buscar o schedule que corresponde ao ID e ao dia da semana
        $schedule = $this->schedule
                         ->where('id', $request->time)
                         ->where('buffet_id', $buffet->id)
                         ->where('day_week', $dayOfWeek) // Verificar se o dia da semana bate
                         ->first();
    
        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 422);
        }
    
        if ($schedule->status !== ScheduleStatus::ACTIVE->name) {
            return response()->json(['message' => 'Schedule is not active'], 422);
        }
    
        // Verificar se há reserva para o horário
        $booking_exists_in_time = $this->booking
            ->where('buffet_id', $buffet->id)
            ->where('party_day', $date)
            ->where('schedule_id', $schedule->id)
            ->where('status', BookingStatus::APPROVED->name)
            ->first();
    
        if (!$booking_exists_in_time) {
            return response()->json(['message' => 'Horário Disponível!'], 200);
        }
    
        // Lógica para horários alternativos
        $alternativas = [];
    
        // 1. Buscar o mesmo horário nas semanas anterior e seguinte (para o mesmo dia da semana)
        $semanaAnterior = (clone $date)->modify('-7 days');
        $semanaSeguinte = (clone $date)->modify('+7 days');
    
        $horariosAlternativos = $this->buscarHorariosAlternativos($buffet->id, $schedule, [$semanaAnterior, $semanaSeguinte], $dayOfWeek);
    
        // 2. Buscar o mesmo horário em dias próximos (7 dias antes e 7 dias depois, sem restrição de dia da semana)
        $diasAntes = 7;
        $diasDepois = 7;
    
        for ($i = 1; $i <= $diasAntes; $i++) {
            $diaAnterior = (clone $date)->modify("-{$i} days");
            $alternativas[] = $this->buscarHorariosAlternativos($buffet->id, $schedule, [$diaAnterior], null); // Sem filtro de dia da semana
        }
    
        for ($i = 1; $i <= $diasDepois; $i++) {
            $diaPosterior = (clone $date)->modify("+{$i} days");
            $alternativas[] = $this->buscarHorariosAlternativos($buffet->id, $schedule, [$diaPosterior], null); // Sem filtro de dia da semana
        }
    
        // 3. Buscar outros horários no mesmo dia
        $outrosHorarios = $this->buscarHorariosNoMesmoDia($buffet->id, $date, $dayOfWeek, $schedule);
        if (!empty($outrosHorarios)) {
            $alternativas = array_merge($alternativas, $outrosHorarios);
        }
    
        // Retornar alternativas, removendo nulos e consolidando resultados
        $alternativas = array_filter($alternativas);
    
        if (!empty($alternativas)) {
            return response()->json([
                'message' => 'Horário Indisponível!',
                'alternativas' => $alternativas
            ], 422);
        }
    
        return response()->json(['message' => 'Horário Indisponível e sem alternativas!'], 422);
    }
    
    // Função para buscar horários alternativos em dias diferentes, para o mesmo dia da semana ou intervalo de dias
    private function buscarHorariosAlternativos($buffetId, $schedule, $datas, $dayOfWeek = null) {
        foreach ($datas as $data) {
            if ($dayOfWeek && strtoupper($data->format('l')) !== $dayOfWeek) {
                continue; // Se for necessário filtrar pelo dia da semana, ignorar dias diferentes
            }
    
            $bookingExists = $this->booking
                ->where('buffet_id', $buffetId)
                ->where('party_day', $data)
                ->where('schedule_id', $schedule->id)
                ->where('status', BookingStatus::APPROVED->name)
                ->first();
    
            if (!$bookingExists) {
                return [
                    'data' => $data->format('Y-m-d'),
                    'horario' => [
                        'id'=> $schedule->id,
                        'comeco' => $schedule->start_time,
                        'duracao' => $schedule->duration
                    ] // Detalhes do horário
                ];
            }
        }
        return null;
    }
    
    // Função para buscar outros horários disponíveis no mesmo dia, mas para o mesmo dia da semana
    private function buscarHorariosNoMesmoDia($buffetId, $date, $dayOfWeek, $scheduleAtual) {
        $horariosAlternativos = [];
    
        $horarios = $this->schedule
            ->where('buffet_id', $buffetId)
            ->where('status', ScheduleStatus::ACTIVE->name)
            ->where('day_week', $dayOfWeek) // Apenas horários do mesmo dia da semana
            ->where('id', '!=', $scheduleAtual->id ?? null) // Excluir o horário atual
            ->get();
    
        foreach ($horarios as $horario) {
            $bookingExists = $this->booking
                ->where('buffet_id', $buffetId)
                ->where('party_day', $date)
                ->where('schedule_id', $horario->id)
                ->where('status', BookingStatus::APPROVED->name)
                ->first();
    
            if (!$bookingExists) {
                $horariosAlternativos[] = [
                    'data' => $date->format('Y-m-d'),
                    'horario' => [
                        'id'=> $horario->id,
                        'comeco' => $horario->start_time,
                        'duracao' => $horario->duration
                        ]// Detalhes do horário
                ];
            }
        }
    
        return $horariosAlternativos;
    }

    private function ajustarAnoParaProximoOuAtual(DateTime $dataInserida): DateTime {
        // Pega a data atual
        $hoje = new DateTime();
    
        // Clona a data inserida para evitar modificá-la diretamente
        $dataAjustada = clone $dataInserida;
    
        // Atualiza o ano da data inserida para o ano atual
        $dataAjustada->setDate($hoje->format('Y'), $dataInserida->format('m'), $dataInserida->format('d'));
    
        // Se a data ajustada (com o ano atual) já tiver passado em relação a hoje,
        // então definimos o ano para o próximo ano
        if ($dataAjustada < $hoje) {
            $dataAjustada->modify('+1 year');
        }
    
        return $dataAjustada;
    }
    
    
    public function api_get_schedules_by_birthday_date(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
    
        if (!$buffet || !$buffet_slug) {
            return response()->json(['message' => 'Buffet not found'], 422);
        }
    
        // Data e dia da semana
        $date = new DateTime($request->birthday);
        $date = $this->ajustarAnoParaProximoOuAtual($date);
        $dayOfWeek = strtoupper($date->format('l')); // Dia da semana em texto (ex: THURSDAY)
    
        if (!DayWeek::is_in_name($dayOfWeek)) {
            return response()->json(['message' => 'Day not found'], 422);
        }

        $isWeekend = DayWeek::isWeekend(DayWeek::getEnumByName($dayOfWeek));
        if(!$isWeekend){

            // $weekendDates = array_map(
            //     fn($date) => $date->format('Y-m-d'),
            //     $this->searchDaysNearbyWeekends($date)
            // );
            // dd($this->searchDaysNearbyWeekends($date));
            $days = $this->searchSchedulesNearbyWeekend($this->searchDaysNearbyWeekends($date), $buffet);
            return response()->json(['message' => 'Horário Disponivel!', 'dates'=>$days], 200);
        }
        else {
            $weekends = $this->getWeekend(DayWeek::getEnumByName($dayOfWeek), $date);
            return response()->json(['message' => 'Horário Disponivel!', 'dates'=>$this->searchSchedulesNearbyWeekend($weekends, $buffet)], 200);
        }
        // $outrosHorarios = $this->buscarHorariosNoMesmoDia($buffet->id, $date, $dayOfWeek, null);

        // return response()->json(['message' => 'Horário Disponivel!', ], 200);
        // return response()->json(['message' => 'Horário Disponivel!', 'dates'=>$outrosHorarios], 200);
    }

    private function searchSchedulesNearbyWeekend(array $dates, Buffet $buffet) {
        $schedulesAvailable = [];
        foreach($dates as $date) {
            $dayOfWeek = strtoupper($date->format('l')); // Dia da semana em texto (ex: THURSDAY)

            $schedules = $this->schedule
            ->leftJoin('bookings', function ($join) use ($date) {
                $join->on('schedules.id', '=', 'bookings.schedule_id')
                    ->where('bookings.party_day', '=', $date);
            })
            ->where(function ($query) {
                $query->whereNull('bookings.schedule_id')
                    ->orWhereIn('bookings.status', [
                        BookingStatus::PENDENT->name,
                        BookingStatus::REJECTED->name,
                        BookingStatus::CANCELED->name,
                    ]);
            })
            ->orderBy('schedules.start_time', 'asc')
            ->where('schedules.status', ScheduleStatus::ACTIVE->name)
            ->where('schedules.buffet_id', $buffet->id)
            ->where('schedules.day_week', $dayOfWeek)
            ->select('schedules.*')
            ->groupBy('schedules.id') // Agrupa pelo campo id
            ->get();

            array_push($schedulesAvailable, ['day'=>$date->format('Y-m-d'), 'schedules'=>$schedules]);
        }
        return $schedulesAvailable;
    }
    
    private function searchDaysNearbyWeekends(DateTime $date) {
        $nextFriday = clone $date;
        $nextSaturday = clone $date;
        $nextSunday = clone $date;
    
        $prevFriday = clone $date;
        $prevSaturday = clone $date;
        $prevSunday = clone $date;
    
        // Calcula os dias seguintes
        $nextFriday->modify('next friday');
        $nextSaturday->modify('next saturday');
        $nextSunday->modify('next sunday');
    
        // Calcula os dias anteriores
        $prevFriday->modify('last friday');
        $prevSaturday->modify('last saturday');
        $prevSunday->modify('last sunday');

        return [
            $nextFriday,
            $nextSaturday,
            $nextSunday,
            $prevFriday,
            $prevSaturday,
            $prevSunday,
            $date
        ];
    }
    
    private function getWeekend(DayWeek $dayOfWeek, DateTime $date){
        // $friday = clone $date;
        // $saturday = clone $date;
        // $sunday = clone $date;

    //     switch ($dayOfWeek){
    //         case DayWeek::SUNDAY:
    //             // RETORNAR -> !2 DIAS ANTERIORES! + !PRÓXIMA SEXTA(+2 PRÓXIMOS DIAS)! + !DOMINGO ANTERIOR(+2 DIAS ANTERIORES)! 
    //             return [
    //                 (clone $date)->modify('last friday - 1 week'),
    //                 (clone $date)->modify('last saturday - 1 week'),
    //                 (clone $date)->modify('last sunday'),
    //                 $friday->modify('last friday'),
    //                 $saturday->modify('last saturday'),
    //                 $date,
    //                 (clone $date)->modify('next friday'),
    //                 (clone $date)->modify('next saturday'),
    //                 (clone $date)->modify('next sunday'),
    //             ];
    //         case DayWeek::SATURDAY:
    //             // RETORNAR -> !1 PRÓXIMO DIA! + !1 DIA ANTERIOR! + !PROXIMA SEXTA(+2 PRÓXIMOS DIAS)! + !DOMINGO ANTERO(+ 2D ANTERIORES)!
    //             return [
    //                 (clone $date)->modify('last friday - 1 week'),
    //                 (clone $date)->modify('last saturday'),
    //                 (clone $date)->modify('last sunday'),
    //                 $friday->modify('last friday'),
    //                 $date,
    //                 $sunday->modify('next sunday'),
    //                 (clone $date)->modify('next friday'),
    //                 (clone $date)->modify('next saturday'),
    //                 (clone $date)->modify('next sunday + 1 week'),
    //             ];            
    //         default:
    //             // RETORNAR -> !2 PRÓXIMOS DIAS! + !PRÓXIMA SEXTA(+2 PRÓXIMOS DIAS)! + !DOMINGO ANTERIOR(+2 DIAS ANTERIORES)! 
    //             return [
    //                 (clone $date)->modify('last friday'),
    //                 (clone $date)->modify('last saturday'),
    //                 (clone $date)->modify('last sunday'),
    //                 $date,
    //                 $saturday->modify('next saturday'),
    //                 $sunday->modify('next sunday'),
    //                 (clone $date)->modify('next friday'),
    //                 (clone $date)->modify('next saturday + 1 week'),
    //                 (clone $date)->modify('next sunday + 1 week'),
    //             ];       
    //     }

    $lastFriday = (clone $date)->modify('last friday');
    $lastSaturday = (clone $date)->modify('last saturday');
    $lastSunday = (clone $date)->modify('last sunday');
    $nextFriday = (clone $date)->modify('next friday');
    $nextSaturday = (clone $date)->modify('next saturday');
    $nextSunday = (clone $date)->modify('next sunday');

    switch ($dayOfWeek) {
        case DayWeek::SUNDAY:
            return [
                (clone $lastFriday)->modify('-1 week'),
                (clone $lastSaturday)->modify('-1 week'),
                $lastSunday,
                $lastFriday,
                $lastSaturday,
                $date,
                $nextFriday,
                $nextSaturday,
                $nextSunday,
            ];
        
        case DayWeek::SATURDAY:
            return [
                (clone $lastFriday)->modify('-1 week'),
                $lastSaturday,
                $lastSunday,
                $lastFriday,
                $date,
                $nextSunday,
                $nextFriday,
                $nextSaturday,
                (clone $nextSunday)->modify('+1 week'),
            ];
        
        default:
            return [
                $lastFriday,
                $lastSaturday,
                $lastSunday,
                $date,
                $nextSaturday,
                $nextSunday,
                $nextFriday,
                (clone $nextSaturday)->modify('+1 week'),
                (clone $nextSunday)->modify('+1 week'),
            ];
        }
    }

}
