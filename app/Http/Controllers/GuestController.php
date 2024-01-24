<?php

namespace App\Http\Controllers;

use App\Enums\GuestStatus;
use App\Models\Booking;
use App\Models\Buffet;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function __construct(
        protected Guest $guest,
        protected Booking $booking,
        protected Buffet $buffet,
    ) {}

    public function create(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();
        $booking = $this->booking->where('id',$request->booking)->get()->first();

        return view('guest.invite',['buffet'=>$buffet,'booking'=>$booking]);
    }

    public function store(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();
        $booking = $this->booking->where('id',$request->booking)->get()->first();

        $guest = $this->guest->create([
            'name'=>$request->name,
            'document'=>$request->document,
            'age'=>$request->age,
            'booking'=>$booking->id,
            'buffet'=>$buffet->id,
            'status'=> GuestStatus::PENDENT->name
        ]);
        return redirect()->route('booking.show',['buffet'=>$buffet_slug,'booking'=>$booking]);
    }

    public function show(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();
        $booking = $this->booking->where('id',$request->booking)->get()->first();
        $guest = $this->guest->where('id',$request->guest)->get()->first();


        return view('guest.show',['buffet'=>$buffet,'booking'=>$booking, 'guest'=>$guest]);

    }

}
