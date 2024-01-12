<?php

namespace App\Http\Controllers;

use App\Enums\BuffetStatus;
use App\Http\Requests\StoreBuffetRequest;
use App\Http\Requests\UpdateBuffetRequest;
use App\Models\Address;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Phone;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class BuffetController extends Controller
{
    public function __construct(
        protected User $user,
        protected Buffet $buffet,
        protected Address $address,
        protected Phone $phone,
        protected Subscription $subscription,
        protected BuffetSubscription $buffet_subscription
    )
    {
        
    }
    public function dashboard() {
        dd('Dashboard do buffet');
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

        $buffet_subscription = $this->buffet_subscription->create([
            'buffet_id'=>$buffet->id,
            'subscription_id'=>$subscription->id
        ]);

        return response()->json(['data'=>[$buffet, $buffet_subscription, $owner]], 201);
        return response()->json(['data'=>'deu bom'], 201);
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
    public function edit(Buffet $buffet)
    {
        //
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
}
