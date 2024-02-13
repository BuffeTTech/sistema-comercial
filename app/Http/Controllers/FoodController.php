<?php

namespace App\Http\Controllers;

use App\Enums\FoodStatus;
use App\Http\Requests\Foods\StoreFoodRequest;
use App\Http\Requests\Foods\UpdateFoodRequest;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Food;
use App\Models\FoodPhoto;
use App\Models\SubscriptionConfiguration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    public function __construct(
        protected Food $food,
        protected FoodPhoto $photo,
        protected Buffet $buffet
    ) {
    }

    public static string $image_repository = '/app/public/foods';

     private function getFoodPhotos($foodId)
    {
        return FoodPhoto::where('food_id', $foodId)->get();
    }   

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        $this->authorize('viewAny', [Food::class, $buffet]);

        $foods = $this->food->where('buffet_id', $buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
        return view('foods.index', ['foods'=>$foods, 'buffet'=>$buffet_slug]);
    }

    public function not_found() {
        return view('food.food-not-found');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        // $this->authorize('create', [Food::class, $buffet]);

        // buffet exists
        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }
        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        return view('foods.create', ['buffet'=>$buffet, 'configurations'=>$configurations]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFoodRequest $request)
    {
        $slug = sanitize_string($request->slug);
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        if($this->food->where('slug', $slug)->where('buffet_id', $buffet->id)->get()->first()){
            return redirect()->back()->withErrors(['slug' => 'Este pacote de comida ja existe.'])->withInput();
        }
        $this->authorize('create', [Food::class, $buffet]);

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }
        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();
        if(!isset($request->images) || count($request->foods_photo) != $configurations['max_food_photos']) {
            return redirect()->back()->withErrors(['foods_photo_generic'=> 'Não existem '.$configurations['max_food_photos'].' fotos na requisição'])->withInput();
        }
        
        $food = $this->food->create([
            "name_food"=>$request->name_food,
            "food_description"=>$request->food_description,
            "beverages_description"=>$request->beverages_description,
            "status"=>$request->status ?? FoodStatus::ACTIVE->name,
            "price"=>$request->price,
            "slug"=>$slug,
            "buffet_id"=>$buffet->id,
        ]);

         if ($request->has('foods_photo')) {
            $foods_photo = $request->foods_photo; 
            foreach($foods_photo as $photo){
                if ($photo->isValid()) {
                    if($upload = $this->upload_image(photo: $photo))  {
                        $this->photo->create([
                            'file_name'=>$upload['file_name'],
                            'file_path'=>$upload['file_path'],
                            'file_extension'=>$upload['file_extension'],
                            'mime_type'=>$upload['mime_type'],
                            'file_size'=>$upload['file_size'],
                            'food_id'=>$food->id,
                        ]);
    
                    } else {
                        return redirect()->route('food.show', ['buffet' => $request->buffet, 'food' => $request->food])->withErrors(['photo'=>"error photo not valid"])->withInput();
                    }
                }
            }
        }

        return redirect()->route('food.show', ['food'=>$food, 'buffet'=>$buffet_slug]); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        if(!$food = $this->food->where('slug', $request->food)->where('buffet_id', $buffet->id)->get()->first()){
            return redirect()->route('food.index', $buffet_slug)->withErrors(['slug' => 'food not found.'])->withInput();
        }
        $this->authorize('view', [Food::class, $food, $buffet]);

        $foods_photo = $this->getFoodPhotos($food->id); 


        // $food = $this->food->where('buffet', $buffet->id);

        return view('foods.show', ['food'=>$food, 'buffet'=>$buffet_slug, 'foods_photo'=> $foods_photo]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        // dd($request->buffet, $request->food); 
        if (!$food = $this->food->where('slug', $request->food)->where('buffet_id', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['slug' => 'food not found.'])->withInput();
            
        }

        $this->authorize('update', [Food::class, $food, $buffet]);

        $foods_photo = $this->getFoodPhotos($food->id); 

        // dd($foods_photo);
        return view('foods.update', ['food'=> $food, 'buffet'=>$buffet, 'foods_photo'=> $foods_photo]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update_photo(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        if (!$food = $this->food->where('slug', $request->food)->where('buffet_id', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['slug' => 'food not found.'])->withInput();
            
        }
        
        if (!$food = $this->food->where('slug', $request->food)->where('buffet_id', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['slug' => 'food not found.'])->withInput();
        }

        $foods_photo =  $this->photo->where('id', $request->foods_photo)->where('food_id', $food->id)->get()->first();
        if(!$foods_photo){
            return redirect()->route('food.index', ['buffet'=>$request->buffet])->withErrors(['photo'=>"Photo not found."])->withInput();
        }

        $this->authorize('update', [Food::class, $food, $buffet]);

        // dd(storage_path(self::$image_repository).$foods_photo->file_path,
        //     Storage::exists(self::$image_repository).$foods_photo->file_path,
        //     Storage::allFiles(self::$image_repository));
    
        $previousFilePath = storage_path(self::$image_repository).$foods_photo->file_path;
        $photo_id = $this->photo->find($foods_photo->id);

         $photo = $request->photo;
         if ($request->has('photo')) {
            if ($photo->isValid()) { 
               
                if($upload = $this->upload_image(photo: $photo))  {
                    // excluir foto anterior aqui
                    if (file_exists($previousFilePath)) {
                        // dd(Storage::delete($previousFilePath));
                        unlink($previousFilePath);
                    }
                    $foods_photo->update([
                        'file_name'=>$upload['file_name'],
                        'file_path'=>$upload['file_path'],
                        'file_extension'=>$upload['file_extension'],
                        'mime_type'=>$upload['mime_type'],
                        'file_size'=>$upload['file_size'],
                        'food_id'=>$food->id,
                    ]);

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
   
    public function update(UpdateFoodRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $food = $this->food->where('slug', $request->food)->where('buffet_id', $buffet->id)->get()->first();
        if(!$food){
            return redirect()->back()->withErrors(['slug' => 'Pacote de comida não encontrado.'])->withInput();
        }

        $food_exists = $this->food->where('slug', $request->slug)->where('buffet_id', $buffet->id)->get()->first();
        if($food_exists && $food_exists->id !== $food->id) {
            return redirect()->back()->withErrors(['slug' => 'Este pacote de comida já existe.'])->withInput();
        }
        $this->authorize('update', [Food::class, $food, $buffet]);

        $food->update([
            "name_food" => $request->name_food,
            "food_description" => $request->food_description,
            "beverages_description" => $request->beverages_description,
            "status" => $request->status ?? FoodStatus::ACTIVE->name,
            "price" => $request->price,
            "slug" => $request->slug,
            "buffet_id" => $buffet->id,
        ]);

        $foods_photo =  $this->photo->where('id', $request->slug)->where('food_id', $food->id)->get()->first(); 

        $pk = $this->food->find($food->id);
        
        return redirect()->route('food.edit', ['buffet'=>$buffet->slug, 'food'=>$pk->slug]);
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Request $request)
     {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        if (!$food = $this->food->where('slug', $request->food)->where('buffet_id', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['slug' => 'food not found.'])->withInput();
        }
        
        $this->authorize('destroy', [Food::class, $food, $buffet]);

        $food->update(['status' => FoodStatus::UNACTIVE->name]);
        
        return redirect()->back()->with(['message' => 'Deletado com sucesso.'])->withInput();
     }

     public function activate_food(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        if (!$food = $this->food->where('slug', $request->food)->where('buffet_id', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['slug' => 'food not found.'])->withInput();
        }

        $this->authorize('change_status', [Food::class, $food, $buffet]);
        
        $food->update(['status' => FoodStatus::ACTIVE->name]);
        
        return redirect()->back()->with(['message' => 'Deletado com sucesso.'])->withInput();
     }

     public function change_status(Request $request)
     {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        $food = $this->food->where('slug', $request->food)->where('buffet_id', $buffet->id)->get()->first();
        if (!$food) {
            return redirect()->back()->withErrors(['slug' => 'food not found.'])->withInput();
        }

        $this->authorize('change_status', [Food::class, $food, $buffet]);

        $food->update(['status'=>$request->status]);

        return redirect()->route('food.index', ['buffet'=>$buffet_slug]);
     }

     // API
     public function api_get_food(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return response()->json(['message' => 'Buffet not found'], 422);
        }
        
        if(!$food = $this->food->where('slug', $request->food)->where('buffet_id', $buffet->id)->with('photos')->get()->first()){
            return response()->json(['message' => 'Food not found'], 422);;
        }

        return response()->json(['data'=>$food], 200);;
     }
}
