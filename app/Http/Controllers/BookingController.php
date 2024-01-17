<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\DayWeek;
use App\Enums\DecorationStatus;
use App\Enums\FoodStatus;
use App\Enums\ScheduleStatus;
use App\Http\Requests\Bookings\StoreBookingRequest;
use App\Http\Requests\Bookings\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Buffet;
use App\Models\Decoration;
use App\Models\Food;
use App\Models\Schedule;
use DateTime;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected Buffet $buffet,
        protected Schedule $schedule,
        protected Booking $booking,
        protected Food $food,
        protected Decoration $decoration
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return null;
        }
        
        // Lista de somente as próximas reservas 
        $bookings =  $this->booking->where('buffet_id', $buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));

        return view('bookings.index', ['bookings'=>$bookings,'buffet' => $buffet]);
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

        $foods = $this->food->where('buffet', $buffet->id)->where('status', FoodStatus::ACTIVE->name)->get();
        $decorations = $this->decoration->where('buffet', $buffet->id)->where('status', DecorationStatus::ACTIVE->name)->get();

        return view('bookings.create', ['buffet'=>$buffet, 'foods'=>$foods, 'decorations'=>$decorations]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }

    // API
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
                    ->orWhere('bookings.status', '=', BookingStatus::REJECTED->name);
            })
            ->orderBy('schedules.start_time', 'asc')
            ->where('schedules.status', ScheduleStatus::ACTIVE->name)
            ->where('schedules.buffet_id', $buffet->id)
            ->where('schedules.day_week', $dayOfWeek)
            ->select('schedules.*')
            ->get();


        return response()->json(['day'=>$date, 'day_week'=>$dayOfWeek, 'schedules'=>$schedules], 200);
    }
}
