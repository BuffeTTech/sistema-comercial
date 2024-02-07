<?php

namespace App\Http\Controllers;

use App\Enums\BuffetStatus;
use App\Http\Requests\StoreBuffetRequest;
use App\Http\Requests\UpdateBuffetRequest;
use App\Models\Address;
use App\Models\Buffet;
use App\Models\BuffetPhoto;
use App\Models\BuffetSubscription;
use App\Models\Phone;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BuffetController extends Controller
{
    public static string $image_repository = '/app/public/buffets';
    public function __construct(
        protected User $user,
        protected Buffet $buffet,
        protected Address $address,
        protected Phone $phone,
        protected Subscription $subscription,
        protected BuffetSubscription $buffet_subscription,
        protected BuffetPhoto $buffet_photo,
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
        return response()->json(['data'=>[$request->buffet_subscription]], 201);
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

        return view('buffet.update',['buffet'=>$buffet]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuffetRequest $request)
    {
        dd('aa', $request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buffet $buffet)
    {
        //
    }

    public function update_logo(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->with('logo')->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $this->authorize('update', [Buffet::class, $buffet]);

        // dd(storage_path(self::$image_repository).$foods_photo->file_path,
        //     Storage::exists(self::$image_repository).$foods_photo->file_path,
        //     Storage::allFiles(self::$image_repository));

        $logo = $this->buffet_photo->where('id', $buffet->logo_id)->get()->first();

        $previousFilePath = "";
        if($logo) {
            $previousFilePath = storage_path(self::$image_repository).$logo->file_path;
        }

         $photo = $request->buffet_logo;
         if ($request->has('buffet_logo')) {
            if ($photo->isValid()) { 
               
                if($upload = $this->upload_image(photo: $photo))  {
                    // excluir foto anterior aqui
                    if (file_exists($previousFilePath)) {
                        // dd(Storage::delete($previousFilePath));
                        unlink($previousFilePath);
                    }

                    if($logo) {
                        $logo->update([
                            'file_name'=>$upload['file_name'],
                            'file_path'=>$upload['file_path'],
                            'file_extension'=>$upload['file_extension'],
                            'mime_type'=>$upload['mime_type'],
                            'file_size'=>$upload['file_size'],
                            'buffet_id'=>$buffet->id,
                        ]);
                    } else {
                        $photo_created = $this->buffet_photo->create([
                            'file_name'=>$upload['file_name'],
                            'file_path'=>$upload['file_path'],
                            'file_extension'=>$upload['file_extension'],
                            'mime_type'=>$upload['mime_type'],
                            'file_size'=>$upload['file_size'],
                            'buffet_id'=>$buffet->id,
                        ]);
                        $buffet->update(['logo_id'=>$photo_created->id]);
                    }

                } else {
                    return redirect()->back()->withErrors(['photo'=>"error photo not valid"])->withInput();
                }
            }
        }

        return redirect()->back()->withInput();
    }

    private function upload_image($photo) {
        if ($photo->isValid()) {
            $file_name = $photo->getClientOriginalName();
            $file_extension = $photo->getClientOriginalExtension();
            $file_size = $photo->getSize();
            $mime_type = $photo->getMimeType();
            
            $imageName = sanitize_string(explode($file_extension, $file_name)[0]).time() . rand(1, 99) . '-.' . $file_extension;
            $file_path = "/".$imageName;

            // $foto->move(public_path('uploads'), $file_path);
            $photo->move(storage_path(self::$image_repository), $imageName);

            //$file_path = "/foods/".$imageName;

            return [
                "file_name"=>$file_name,
                "file_extension"=>$file_extension,
                "file_size"=>$file_size,
                "mime_type"=>$mime_type,
                "file_path"=>$file_path,
            ];
        }
        return null;
    }
}
