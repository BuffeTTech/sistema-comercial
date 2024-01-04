<?php

namespace App\Http\Controllers;

use App\Enums\FoodStatus;
use App\Http\Requests\Foods\StoreFoodRequest;
use App\Http\Requests\Foods\UpdateFoodRequest;
use App\Models\Buffet;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function __construct(
        protected Food $food
    ) {
    }

    // private function update_image(Request $request) {
    //     if (isset($request->files)) {
    //         if(!$this->package->where('slug', $request->slug)->get()->first()){
    //             return redirect()->route('packages.not_found');
    //         }
            
    //         $photo = 'photo_'.$dto->image_id;
    
    //         return $food->where('slug',$dto->slug)->update([$photo=>$dto->photo]);
    
    //         return back();
    //     }
    //     $retornos = new MessageBag();
    //     $retornos->add('errors', 'Imagem não enviada');
    //     return back()->withErrors($retornos);
    // }

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
            return redirect()->back()->with('errors', 'food already exists');
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

        // if (isset($request->images)) {
             // if (count($request->images) !== 3) {
             //     throw new ValueError('Has less than 3 images');
             // }
        //     $image_index = 1;
        //     foreach ($request->images as $image) {
        //         $img_db = 'photo_' . $image_index;
        //         $image_index++;
        //         $request->$img_db = $this->uploadImage($image);
        //     }
        //     unset($request->images);
        // }

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
            return redirect()->route('food.index', $buffet_slug)->with('errors', 'food not found');
        }

        // $food = $this->food->where('buffet', $buffet->id);

        return view('foods.show', ['food'=>$food, 'buffet'=>$buffet_slug]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        // dd($request->buffet, $request->food); 
        if (!$food = $this->food->where('slug', $request->food)->where('buffet', $buffet->id)->get()->first() ) {
            return redirect()->back()->with('errors', 'food not found');
            
        }

        return view('foods.update', ['food'=> $food, 'buffet'=>$buffet]);
    }

    /**
     * Update the specified resource in storage.
     */
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

        $pk = $this->food->find($food->id);

        return redirect()->route('food.show', ['food'=>$pk->slug, 'buffet'=>$buffet_slug]);
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
            return redirect()->back()->with('errors', 'food not found');
        }

        $food->update(['status'=>$request->status]);

        return redirect()->route('food.index', ['buffet'=>$buffet_slug]);
     }
}
