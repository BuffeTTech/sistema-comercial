<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
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
            abort(404);
            // return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $booking_id = $this->hashids->decode($request->booking);
        if(!$booking_id) {
            abort(404);
            // return redirect()->back()->withErrors(['message'=>'Reserva não encontrada'])->withInput();
        }
        
        $booking_id = $booking_id[0];

        $booking = $this->booking->where('id',$booking_id)->get()->first();
        
        if(!$booking) {
            abort(404);
            // return redirect()->back()->withErrors(['booking'=>'Reserva não encontrada'])->withInput();
        }

        // $this->authorize('create', [Guest::class, $booking, $buffet]);

        if($booking->status !== BookingStatus::APPROVED->name) {
            abort(401);
            // return redirect()->back()->withErrors(['booking'=>'Esta festa não está aceitando convidados'])->withInput();
        }

        return view('guest.invite',['buffet'=>$buffet,'booking'=>$booking]);
    }

    public function store(StoreGuestRequest $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            // abort(404);
            return redirect()->back()->withErrors(['error'=>'Buffet não encontrado'])->withInput();
        }

        $booking_id = $this->hashids->decode($request->booking);
        if(!$booking_id) {
            // abort(404);
            return redirect()->back()->withErrors(['error'=>'Reserva não encontrada'])->withInput();
        }
        
        $booking_id = $booking_id[0];
        
        $booking = $this->booking->where('id',$booking_id)->get()->first();
        if(!$booking) {
            // abort(404);
            return redirect()->back()->withErrors(['error'=>'Reserva não encontrada'])->withInput();
        }
        
        $this->authorize('create', [Guest::class, $booking, $buffet]);

        if($booking->status !== BookingStatus::APPROVED->name) {
            // abort(401);
            return redirect()->back()->withErrors(['error'=>'Esta festa não está aceitando convidados'])->withInput();
        }

        $rows = $request->rows;
        $rows_to_insert = [];

        foreach ($rows as $key=>$guest) {
            $guest_exists = $this->guest
                             ->where('document',$guest['document'])
                             ->where('buffet_id', $buffet->id)
                             ->where('booking_id', $booking->id)
                             ->get()
                             ->first();

            if($guest_exists) {
                return redirect()->back()->withErrors(['error'=>'O convidado '.$guest['name'].' do CPF '.$guest['document'].' já está na festa com o status '.GuestStatus::getEnumByName($guest_exists->status)->value])->withInput();
            }

            $data = [
                "name" => $guest['name'],
                "document" => $guest['document'],
                "age" => $guest['age'],
                "status" => $guest->status ?? GuestStatus::PENDENT->name,
                "booking_id" => $booking->id,
                'buffet_id'=>$buffet->id
            ];

            array_push($rows_to_insert, $data);
        }

        foreach ($rows_to_insert as $guest) {
            $this->guest->create($guest);
        }

        if(isset($rows[0]['status']) && $rows[0]['status'] == GuestStatus::EXTRA->name) {
            return redirect()->back()->with(['error'=>"Convidado adicionado com sucesso"]);
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

        $booking_id = $this->hashids->decode($request->booking);
        if(!$booking_id) {
            abort(404);
            // return redirect()->back()->withErrors(['message'=>'Reserva não encontrada'])->withInput();
        }
        
        $booking_id = $booking_id[0];

        $booking = $this->booking->where('id',$booking_id)->get()->first();
        if(!$booking) {
            return redirect()->back()->withErrors(['booking_id'=>'Reserva não encontrada'])->withInput();
        }
        
        $guest_id = $this->hashids->decode($request->guest)[0];
        
        $guest = $this->guest->where('id',$guest_id)->get()->first();
        if(!$guest) {
            return redirect()->back()->withErrors(['guest'=>'Convidado não encontrado'])->withInput();
        }
        
        $this->authorize('change_status', [Guest::class, $booking, $guest, $buffet]);
        
        $guest->update([
            'status'=>$request->status
        ]);


        return redirect()->back();
    }

}
