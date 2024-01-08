<?php

namespace App\Http\Controllers;

use App\Enums\FoodStatus;
use App\Http\Requests\Foods\StoreFoodRequest;
use App\Http\Requests\Foods\UpdateFoodRequest;
use App\Models\Buffet;
use App\Models\Food;
use App\Models\FoodPhoto; 
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

    public static string $image_repository = 'app/public/foods';

     private function getFoodPhotos($foodId)
    {
        return FoodPhoto::where('food', $foodId)->get();
    }   

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return null;
        }

        $buffet = Buffet::where('slug', $request->buffet)->get()->first();
        $foods = $this->food->where('buffet', $buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
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
            return null;
        }

        // buffet exists

        return view('foods.create', ['buffet'=>$buffet]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFoodRequest $request)
    {
        $slug = str_replace(' ', '-', $request->slug);
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        if($this->food->where('slug', $request->food)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->back()->withErrors(['slug' => 'food already exists.'])->withInput();
        }

        // if(!isset($request->images) || count($request->images) != 3) {
        //     return redirect()->back()->with('errors', 'Não existem 3 fotos na requisição');
        // }
        
        $food = $this->food->create([
            "name_food"=>$request->name_food,
            "food_description"=>$request->food_description,
            "beverages_description"=>$request->beverages_description,
            "status"=>$request->status ?? FoodStatus::ACTIVE->name,
            "price"=>$request->price,
            "slug"=>$slug,
            "buffet"=>$buffet->id,
        ]);

        if ($request->has('foods_photo')) {
            foreach ($request->file('foods_photo') as $foto) {
                if ($foto->isValid()) {
                    Storage::delete($foto);
                    $file_name = $foto->getClientOriginalName();
                    $file_extension = $foto->getClientOriginalExtension();
                    $file_size = $foto->getSize();
                    $mime_type = $foto->getMimeType();

                    $imageName = sanitize_string(explode($file_extension, $file_name)[0]).time() . rand(1, 99) . '-.' . $file_extension;
                    // $imageName = sanitize_string(explode($file_extension, $file_name)[0]) . '-' . time() . rand(1, 99) . '.' . $file_extension;
                    $file_path = "/".$imageName;

                    $this->photo->create([
                        'file_name'=>$file_name,
                        'file_path'=>$file_path,
                        'file_extension'=>$file_extension,
                        'mime_type'=>$mime_type,
                        'file_size'=>$file_size,
                        'food'=>$food->id,
                    ]);


                    // $foto->move(public_path('uploads'), $file_path);
                    $foto->move(storage_path(self::$image_repository), $imageName);
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
        
        if(!$food = $this->food->where('slug', $request->food)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->route('food.index', $buffet_slug)->withErrors(['slug' => 'food not found.'])->withInput();
        }

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
        // dd($request->buffet, $request->food); 
        if (!$food = $this->food->where('slug', $request->food)->where('buffet', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['slug' => 'food not found.'])->withInput();
            
        }

        $foods_photo = $this->getFoodPhotos($food->id); 

        // dd($foods_photo);
        return view('foods.update', ['food'=> $food, 'buffet'=>$buffet, 'foods_photo'=> $foods_photo]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update_photo(Request $request)
    {
        $food_slug = $request->food;
        $food = Food::where('slug', $food_slug)->first();
        $foods_photo =  $this->photo->where('id', $request->foods_photo)->where('food', $food->id)->get()->first();
        if(!$foods_photo){
            return redirect()->route('food.index', ['buffet'=>$request->buffet])->withErrors(['photo'=>"Photo not found."])->withInput();
        }
        
         $photo_id = $this->photo->find($foods_photo->id);

         $photo = $request->photo;
         if ($request->has('photo')) {
            if ($photo->isValid()) {
                if($upload = $this->upload_image(photo: $photo))  {
                    // excluir foto anterior aqui
                    $foods_photo->update([
                        'file_name'=>$upload['file_name'],
                        'file_path'=>$upload['file_path'],
                        'file_extension'=>$upload['file_extension'],
                        'mime_type'=>$upload['mime_type'],
                        'file_size'=>$upload['file_size'],
                        'food'=>$food->id,
                    ]);

                } else {
                    return redirect()->route('food.show', ['buffet' => $request->buffet, 'food' => $request->food])->withErrors(['photo'=>"error photo not valid"])->withInput();
                }
            }
        }
        return redirect()->route('food.show', ['buffet' => $request->buffet, 'food' => $request->food]);
    }

    private function upload_image($photo) {
        if ($photo->isValid()) {
            $file_name = $photo->getClientOriginalName();
            $file_extension = $photo->getClientOriginalExtension();
            $file_size = $photo->getSize();
            $mime_type = $photo->getMimeType();
            
            $imageName = sanitize_string(explode($file_extension, $file_name)[0]).time() . rand(1, 99) . '-.' . $file_extension;
            // $imageName = sanitize_string(explode($file_extension, $file_name)[0]) . '-' . time() . rand(1, 99) . '.' . $file_extension;
            $file_path = "/".$imageName;

            // $foto->move(public_path('uploads'), $file_path);
            $photo->move(storage_path(self::$image_repository), $imageName);

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

        $food = $this->food->where('slug', $request->food)->where('buffet', $buffet->id)->get()->first();
        if(!$food){
            return redirect()->back()->withErrors(['slug' => 'food not found.'])->withInput();
        }

        $food_exists = $this->food->where('slug', $request->slug)->where('buffet', $buffet->id)->get()->first();
        if($food_exists && $food_exists->id !== $food->id) {
            return redirect()->back()->withErrors(['slug' => 'food already exists.'])->withInput();
        }

        $food->update([
            "name_food" => $request->name_food,
            "food_description" => $request->food_description,
            "beverages_description" => $request->beverages_description,
            "status" => $request->status ?? FoodStatus::ACTIVE->name,
            "price" => $request->price,
            "slug" => $request->slug,
            "buffet" => $buffet->id,
        ]);

        $foods_photo =  $this->photo->where('id', $request->slug)->where('food', $food->id)->get()->first(); 

        $pk = $this->food->find($food->id);

        return redirect()->route('food.show', ['food'=>$pk->slug, 'buffet'=>$buffet_slug, 'foods_photo'=> $foods_photo]);
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Food $food, Request $request)
     {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
         if ($food = $this->food->where('slug', $request->food)->where('buffet', $buffet->id)->get()->first()) {
             return redirect()->route('food.not_found');
         }

         $food->update(['status' => FoodStatus::UNACTIVE->name]);

         return redirect()->route('food.index', ['buffet'=>$buffet_slug]);
     }

     public function change_status(Request $request)
     {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        $food = $this->food->where('slug', $request->food)->where('buffet', $buffet->id)->get()->first();
        if (!$food) {
            return redirect()->back()->withErrors(['slug' => 'food not found.'])->withInput();
        }

        $food->update(['status'=>$request->status]);

        return redirect()->route('food.index', ['buffet'=>$buffet_slug]);
     }
}
