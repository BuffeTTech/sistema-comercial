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

        $booking_id = $this->hashids->decode($request->booking)[0];

        $booking = $this->booking->where('id',$booking_id)->get()->first();
        
        if(!$booking) {
            return redirect()->back()->withErrors(['booking'=>'Reserva não encontrada'])->withInput();
        }

        $this->authorize('create', [Guest::class, $booking, $buffet]);

        return view('guest.invite',['buffet'=>$buffet,'booking'=>$booking]);
    }

    public function store(StoreGuestRequest $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        $booking_id = $this->hashids->decode($request->booking)[0];
        
        $booking = $this->booking->where('id',$booking_id)->get()->first();
        if(!$booking) {
            return redirect()->back()->withErrors(['booking'=>'Reserva não encontrada'])->withInput();
        }
        
        $this->authorize('create', [Guest::class, $booking, $buffet]);

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

        if($request->status == GuestStatus::PRESENT->name) {
            return redirect()->back()->with(['message'=>"Convidado adicionado com sucesso"]);
        } else {
            return view('guest.guest_invited', ['buffet'=>$buffet, 'booking'=>$booking, 'guest'=>$guest]);
        }
    }

    public function change_status(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $booking_id = $this->hashids->decode($request->booking)[0];
        
        $booking = $this->booking->where('id',$booking_id)->get()->first();
        if(!$booking) {
            return redirect()->back()->withErrors(['booking_id'=>'Reserva não encontrada'])->withInput();
        }
        
        $this->authorize('change_status', [Guest::class,$booking, $buffet]);

        $guest_id = $this->hashids->decode($request->guest)[0];

        $guest = $this->guest->where('id',$guest_id)->get()->first();
        if(!$guest) {
            return redirect()->back()->withErrors(['guest'=>'Convidado não encontrado'])->withInput();
        }
        
        $guest->update([
            'status'=>$request->status
        ]);


        return redirect()->back();
    }

}
