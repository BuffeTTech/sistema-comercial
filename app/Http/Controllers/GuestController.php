<?php

namespace App\Http\Controllers;

use App\Enums\GuestStatus;
use App\Http\Requests\Guests\StoreGuestRequest;
use App\Models\Booking;
use App\Models\Buffet;
use App\Models\Guest;
use Hashids\Hashids;
use Illuminate\Http\Request;

class GuestController extends Controller
{    
    protected Hashids $hashids;

    public function __construct(
        protected Guest $guest,
        protected Booking $booking,
        protected Buffet $buffet,
    ) {
        $this->hashids = new Hashids(config('app.name'));
    }

    public function create(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $booking = $this->booking->where('id',$request->booking)->get()->first();
        if(!$booking) {
            return redirect()->back()->withErrors(['booking'=>'Reserva não encontrada'])->withInput();
        }

        return view('guest.invite',['buffet'=>$buffet,'booking'=>$booking]);
    }

    public function store(StoreGuestRequest $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $booking = $this->booking->where('id',$request->booking)->get()->first();
        if(!$booking) {
            return redirect()->back()->withErrors(['booking'=>'Reserva não encontrada'])->withInput();
        }

        $guest_exists = $this->guest
                             ->where('document',$request->guest)
                             ->where('buffet_id', $buffet->id)
                             ->where('booking_id', $booking->id)
                             ->get()
                             ->first();
        if($guest_exists) {
            return redirect()->back()->withErrors(['document'=>'Guest already exists'])->withInput();
        }

        $guest = $this->guest->create([
            'name'=>$request->name,
            'document'=>$request->document,
            'age'=>$request->age,
            'booking_id'=>$booking->id,
            'buffet_id'=>$buffet->id,
            'status'=> $request->status ?? GuestStatus::PENDENT->name
        ]);

        return view('guest.guest_invited', ['buffet'=>$buffet, 'booking'=>$booking, 'guest'=>$guest]);
    }

    public function show(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $booking = $this->booking->where('id',$request->booking)->get()->first();
        if(!$booking) {
            return redirect()->back()->withErrors(['booking_id'=>'Reserva não encontrada'])->withInput();
        }

        $guest = $this->guest->where('id',$request->guest)->get()->first();
        if(!$guest) {
            return redirect()->back()->withErrors(['guest'=>'Convidado não encontrado'])->withInput();
        }

        return view('guest.show',['buffet'=>$buffet,'booking'=>$booking, 'guest'=>$guest]);

    }

    public function change_status(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $booking = $this->booking->where('id',$request->booking)->get()->first();
        if(!$booking) {
            return redirect()->back()->withErrors(['booking'=>'Reserva não encontrada'])->withInput();
        }

        $guest = $this->guest->where('id',$request->guest)->get()->first();
        if(!$guest) {
            return redirect()->back()->withErrors(['guest'=>'Convidado não encontrado'])->withInput();
        }
        
        $guest->update([
            'status'=>$request->status
        ]);


        return redirect()->back();
    }

}
