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
use App\Models\Decoration;
use App\Models\Food;
use App\Models\Guest;
use App\Models\Schedule;
use Carbon\Carbon;
use DateTime;
use Hashids\Hashids;
use Illuminate\Http\Request;

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
    )
    {
        $this->hashids = new Hashids(config('app.name'));
    }

    private static int $min_days = 5;

    private function current_party(){
        // Lista de somente as próximas reservas 
        $bookings = $this->booking
            ->with(['schedule'=>function ($query) {
                $query->orderBy('start_time', 'asc');
            }, 'food','decoration', 'user'])
            ->where('status', BookingStatus::APPROVED->name)
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

    /**
     * Display a listing of the resource.
     */

    public function list(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return null;
        }

        $format = $request->get('format', 'all');
        $status = BookingStatus::PENDENT->name; 
        if($format == 'pendent') {
            $bookings = $this->booking->with(['schedule'=>function ($query) {
                $query->orderBy('start_time', 'asc');
            }, 'food','decoration', 'user'])->where('status', $status)->where('party_day', '>=', date('Y-m-d'))->orderBy('party_day', 'asc')->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
            $this->authorize('viewPendentBookings', [Booking::class, $buffet]);
        } else {
            $format = 'all';
            $bookings =  $this->booking->where('buffet_id', $buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
            $this->authorize('viewAllBookings', [Booking::class, $buffet]);
        }

        $min_days = self::$min_days; 
        return view('bookings.list', ['bookings'=>$bookings,'buffet' => $buffet, 'min_days'=>$min_days, 'format'=>$format]);
    }
    
     
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return null;
        }
        
        // Lista de somente as próximas reservas 
        $bookings = $this->booking->with(['schedule'=>function ($query) {
            $query->orderBy('start_time', 'asc');
        }, 'food','decoration', 'user'])->where('status', BookingStatus::APPROVED->name)->where('party_day', '>=', date('Y-m-d'))->orderBy('party_day', 'asc')->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));

        $this->authorize('viewNextBookings', [Booking::class, $buffet]);
        $current_party = $this->current_party();

        return view('bookings.index', ['bookings'=>$bookings,'buffet' => $buffet, 'current_party'=>$current_party]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return null;
        }
        $this->authorize('create', [Booking::class, $buffet]);

        $foods = $this->food->where('buffet', $buffet->id)->where('status', FoodStatus::ACTIVE->name)->get();
        $decorations = $this->decoration->where('buffet', $buffet->id)->where('status', DecorationStatus::ACTIVE->name)->get();

        return view('bookings.create', ['buffet'=>$buffet, 'foods'=>$foods, 'decorations'=>$decorations]);
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
        $max_date = Carbon::parse($today_date)->addDays(self::$min_days);

        if($max_date > $party_start) {
            return redirect()->back()->withErrors(['party_day'=>"Party should be scheduled with a minimum of ".self::$min_days." days"])->withInput();
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
        ->where('buffet', $buffet->id)
        ->where('status', FoodStatus::ACTIVE->name)
        ->get()
        ->first();
        
        if(!$food) {
            return redirect()->back()->withErrors(['food_id'=>"Food not found"])->withInput();
        }
        
        // Valida o pacote de decorações
        $decoration = $this->decoration
            ->where('slug', $request->decoration_id)
            ->where('buffet', $buffet->id)
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

        return redirect()->route('booking.show', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        $booking_id = $this->hashids->decode($request->booking)[0];

        $booking = $this->booking
                    ->where('id',$booking_id)
                    ->where('buffet_id', $buffet->id)
                    ->with(['food', 'decoration', 'schedule'])->get()->first();
        
        $guests = $this->guest
                        ->where('booking_id',$booking_id)
                        ->where('buffet_id', $buffet->id)
                        ->get();
        
        $this->authorize('view', [Booking::class, $booking, $buffet]);

        return view('bookings.show', ['buffet'=>$buffet,'booking'=>$booking, 'guests'=>$guests]);
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

        $booking_id = $this->hashids->decode($request->booking)[0];
        
        $booking = $this->booking->where('id', $booking_id)->where('buffet_id', $buffet->id)->get()->first();
        
        if(!$booking) {
            return redirect()->back()->withErrors(['errors'=>'Booking not found'])->withInput();
        }

        $this->authorize('update', [Booking::class, $booking, $buffet]);

        $foods = $this->food->where('buffet', $buffet->id)->where('status', FoodStatus::ACTIVE->name)->get();
        $decorations = $this->decoration->where('buffet', $buffet->id)->where('status', DecorationStatus::ACTIVE->name)->get();

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

        $booking_id = $this->hashids->decode($request->booking)[0];

        // valida se o booking existe
        $booking = $this->booking->where('buffet_id', $buffet->id)->where('id', $booking_id)->get()->first();
        if(!$booking) {
            return redirect()->back()->withErrors(['errors'=>'Booking not found'])->withInput();
        }
        
        $this->authorize('update', [Booking::class, $booking, $buffet]);

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
        $max_date = Carbon::parse($today_date)->addDays(self::$min_days);

        if($max_date > $party_start) {
            return redirect()->back()->withErrors(['party_day'=>"Uma festa só pode ser agendada ou editada com ".self::$min_days." dias de antecedencia."])->withInput();
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
            ->where('buffet', $buffet->id)
            ->where('status', FoodStatus::ACTIVE->name)
            ->get()
            ->first();

        if(!$food) {
            return redirect()->back()->withErrors(['food_id'=>"Pacote de comida não encontrado"])->withInput();
        }

        // Valida o pacote de decorações
        $decoration = $this->decoration
            ->where('slug', $request->decoration_id)
            ->where('buffet', $buffet->id)
            ->where('status', DecorationStatus::ACTIVE->name)
            ->get()
            ->first();

        if(!$decoration) {
            return redirect()->back()->withErrors(['decoration_id'=>"Pacote de decoração não encontrado"])->withInput();
        }

        // Cria a reserva
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
            'status'=>BookingStatus::PENDENT->name
        ]);

        event(new BookingUpdatedEvent($booking));

        return redirect()->route('booking.edit', ['buffet'=>$buffet->slug, 'booking'=>$booking->id]);
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

        $booking_id = $this->hashids->decode($request->booking)[0];

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
            $booking = $this->booking->where('id', $request->booking)->get()->first();
            if(!$booking) {
                return response()->json(['message' => 'Booking not found'], 422);
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
        
        $current_party = $this->current_party();
        if(!$current_party) {
            // a propria blade tem um if pra validar se existe ou não
            return view('bookings.party_mode',['booking'=>$current_party,'buffet'=>$buffet_slug]);
        }

        $guests = $this->guest
                       ->where('booking_id',$current_party->id)
                       ->where('buffet_id', $buffet->id)
                       ->get();

        $unblocked_guests = $this->guest
                               ->where('booking_id',$current_party->id)
                               ->where('buffet_id', $buffet->id)
                               ->where('status','!=', GuestStatus::BLOCKED->name)
                               ->get();
        $present_guests  = $this->guest
                               ->where('booking_id',$current_party->id)
                               ->where('buffet_id', $buffet->id)
                               ->where('status',GuestStatus::PRESENT->name)
                               ->get();

        $guest_counter = ['present'=>$present_guests->count(), 'unblocked'=>$unblocked_guests->count()];

        return view('bookings.party_mode',['booking'=>$current_party,'buffet'=>$buffet_slug, 'guests'=>$guests, 'guest_counter'=>$guest_counter]);
    }
}
