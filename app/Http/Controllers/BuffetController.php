<?php

namespace App\Http\Controllers;

use App\Enums\BuffetStatus;
use App\Enums\DayWeek;
use App\Http\Requests\StoreBuffetRequest;
use App\Http\Requests\UpdateBuffetRequest;
use App\Models\Address;
use App\Models\Buffet;
use App\Models\BuffetSchedule;
use App\Models\BuffetSubscription;
use App\Models\Phone;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BuffetController extends Controller
{
    public function __construct(
        protected User $user,
        protected Buffet $buffet,
        protected Address $address,
        protected Phone $phone,
        protected Subscription $subscription,
        protected BuffetSubscription $buffet_subscription,
        protected BuffetSchedule $buffet_schedule
    )
    {
        
    }
    public function dashboard(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        return view('dashboard_buffet', ['buffet'=>$buffet]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Buffet $buffet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->with(['buffet_phone1','buffet_phone2', 'buffet_address'])->where('slug', $buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        $buffet_schedules = $this->buffet_schedule->where('buffet_id', $buffet->id)->get();

        return view('buffet.update',['buffet'=>$buffet, 'buffet_schedules'=>$buffet_schedules]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuffetRequest $request, Buffet $buffet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buffet $buffet)
    {
        //
    }

    /**
     *
     * Cadastra um buffet
     *
     * @param   Request  $request Requisição http
     * 
     * @var     $request->subscription Dados do pacote escolhido
     * @var     $request->buffet Dados do buffet
     * @var     $request->buffet_subscription Pacote escolhido e informações de pagamento
     * @var     $request->user Dono do buffet
     * 
     * @return  Response
     *
     */
    public function store_buffet_api(Request $request) {
        $subscription = $this->subscription->where('slug', $request->subscription['slug'])->get()->first();
        if(!$subscription) {
            return response()->json(['message'=>'subscription not found'], 404);
        }
        $buffet_slug = $this->buffet->where('slug', $request->buffet['slug'])->get()->first();
        if ($buffet_slug) {
            return response()->json(['message' => 'Buffet already exists'], 422);
        }

        $buffet_email = $this->buffet->where('email', $request->buffet['email'])->get()->first();
        if ($buffet_email) {
            return response()->json(['message' => 'Buffet already exists'], 422);
        }

        // Valida o owner e o cadastra caso não exista
        $owner = $this->user->where('email', $request->user['email'])->where('buffet_id', null)->get()->first();
        if(!$owner) {
            $user = $request->user;
            $owner = $this->user->create([
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => $user['email_verified_at'],
                'password' => $user['password'],
                'document' => $user['document'],
                'document_type' => $user['document_type'],
                'status' => $user['status'],
                'buffet_id' => null,
            ]);

            $phone1 = $request->user['phone1'];
            if($phone1) {
                $phone1 = $this->phone->create(['number'=>$request->user['phone1']['number']]);
            }
            $phone2 = $request->user['phone1'];
            if($phone2) {
                $phone2 = $this->phone->create(['number'=>$request->user['phone1']['number']]);
            }
            $address = $request->user['address'];
            if($address) {
                $address = $this->address->create([
                    'zipcode' => $address['zipcode'], 
                    'street' => $address['street'], 
                    'number' => $address['number'], 
                    'complement' => $address['complement'] ?? null, 
                    'neighborhood' =>$address['neighborhood'], 
                    'state' => $address['state'], 
                    'city' => $address['city'], 
                    'country' => $address['country']
                ]);
            }
            $owner->update([
                "phone1"=>$phone1->id ?? null,
                "phone2"=>$phone2->id ?? null,
                "address"=>$address->id ?? null,
            ]);

        }

        $buffet = $this->buffet->create([
            'trading_name' => $request->buffet['trading_name'],
            'email' => $request->buffet['email'],
            'document'=>$request->buffet['document'],
            'slug' => $request->buffet['slug'],
            'owner_id' => $owner->id,
            'status'=>BuffetStatus::ACTIVE->name
        ]);
        $phone1 = $request->buffet['phone1'];
        if($phone1) {
            $phone1 = $this->phone->create(['number'=>$request->buffet['phone1']['number']]);
        }
        $phone2 = $request->buffet['phone1'];
        if($phone2) {
            $phone2 = $this->phone->create(['number'=>$request->buffet['phone1']['number']]);
        }
        $address = $request->buffet['address'];
        if($address) {
            $address = $this->address->create([
                'zipcode' => $address['zipcode'], 
                'street' => $address['street'], 
                'number' => $address['number'], 
                'complement' => $address['complement'] ?? null, 
                'neighborhood' =>$address['neighborhood'], 
                'state' => $address['state'], 
                'city' => $address['city'], 
                'country' => $address['country']
            ]);
        }
        $buffet->update([
            "phone1"=>$phone1->id ?? null,
            "phone2"=>$phone2->id ?? null,
            "address"=>$address->id ?? null,
        ]);

        $expires_in = Carbon::parse($request->buffet_subscription['expires_in']);
        $buffet_subscription = $this->buffet_subscription->create([
            'buffet_id'=>$buffet->id,
            'subscription_id'=>$subscription->id,
            'expires_in'=>$expires_in->format('Y-m-d H:i:s')
        ]);

        foreach(DayWeek::names() as $day) {
            $this->buffet_schedule->create([
                'day_week'=>$day,
                'opened'=>false,
                'start'=>null,
                'end'=>null,
                'buffet_id'=>$buffet->id,
            ]);
        }
        return response()->json(['data'=>[$buffet, $buffet_subscription, $owner]], 201);
    }

    public function update_buffet_api(Request $request) {
        
        $buffet = $this->buffet->where('slug', $request->slug)->first();
        if (!$buffet) {
            return response()->json(['message' => 'Buffet not found'], 422);
        }
        
        $buffet_slug = $this->buffet->where('slug', $request->buffet['slug'])->get()->first();
        if ($buffet_slug && $buffet_slug->id !== $buffet->id) {
            return response()->json(['message' => 'Buffet already exists'], 422);
        }
        
        if($request->buffet['phone1']) {
            if($buffet->phone1) {
                $this->phone->find($buffet->phone1)->update(['number'=>$request->buffet['phone1']['number']]);
            } else {
                $buffet->update(['phone1'=>$this->phone->create(['number'=>$request->buffet['phone1']['number']])->id]);
            }
        }
        if($request->buffet['phone2']) {
            if($buffet->phone2) {
                $this->phone->find($buffet->phone2)->update(['number'=>$request->buffet['phone2']['number']]);
            } else {
                $buffet->update(['phone2'=>$this->phone->create(['number'=>$request->buffet['phone2']['number']])->id]);
            }
        }
        
        if($request->buffet['address']) {
            if($buffet->address) {
                $this->address->find($buffet->address)->update([
                    'zipcode' => $request->buffet['address']['zipcode'], 
                    'street' => $request->buffet['address']['street'], 
                    'number' => $request->buffet['address']['number'], 
                    'complement' => $request->buffet['address']['complement'] ?? null, 
                    'neighborhood' => $request->buffet['address']['neighborhood'], 
                    'state' => $request->buffet['address']['state'], 
                    'city' => $request->buffet['address']['city'], 
                    'country' => $request->buffet['address']['country']
                ]);
            } else {
                $address = $this->buffet->create([
                    'zipcode' => $request->buffet['address']['zipcode'], 
                    'street' => $request->buffet['address']['street'], 
                    'number' => $request->buffet['address']['number'], 
                    'complement' => $request->buffet['address']['complement'] ?? null, 
                    'neighborhood' => $request->buffet['address']['neighborhood'], 
                    'state' => $request->buffet['address']['state'], 
                    'city' => $request->buffet['address']['city'], 
                    'country' => $request->buffet['address']['country']
                ]);
                $buffet->update(['address'=> $address->id]);
            }
        }

        $buffet->update([
            'trading_name' => $request->buffet['trading_name'],
            'email' => $request->buffet['email'],
            'slug' => $request->buffet['slug'],
            'document'=>$request->buffet['document'],
            'status'=>$request->buffet['status'] ?? BuffetStatus::ACTIVE->name
        ]); 

        return response()->json(['data'=>[$buffet]], 201); // passar update de inscrição em funcao diferente 
    
    }

    public function delete_buffet_api(Request $request){
        $buffet = $this->buffet->where('slug', $request->slug)->first();

        if($buffet){
            $buffet->update([
                'status' => BuffetStatus::UNACTIVE->name
            ]);
    }
        return response()->json(['data'=>[$buffet]], 201); // passar update de inscrição em funcao diferente 

    }
}
