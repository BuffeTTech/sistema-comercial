<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\DayWeek;
use App\Enums\ScheduleStatus;
use App\Http\Requests\Bookings\StoreBookingRequest;
use App\Http\Requests\Bookings\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Buffet;
use App\Models\Schedule;
use DateTime;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected Buffet $buffet,
        protected Schedule $schedule,
        protected Booking $booking,
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        return view('booking.create', ['buffet'=>$buffet]);
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
                ->select('schedules.*')
            ->where('schedules.buffet_id', $buffet->id)
            ->where('schedules.status', ScheduleStatus::ACTIVE->name)
            ->where('day_week', $dayOfWeek)
            ->get();


        return response()->json(['day'=>$date, 'day_week'=>$dayOfWeek, 'schedules'=>$schedules], 200);
    }
    public function api_get_open_schedules_by_day_and_buffet_update(Request $request) {
        return response()->json(['dataa'=>$request]);

    }
}
