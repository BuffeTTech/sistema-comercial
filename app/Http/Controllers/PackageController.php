<?php

namespace App\Http\Controllers;

use App\Enums\PackageStatus;
use App\Http\Requests\Packages\StorePackageRequest;
use App\Http\Requests\Packages\UpdatePackageRequest;
use App\Models\Buffet;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct(
        protected Package $package
    ) {
    }

    // private function update_image(Request $request) {
    //     if (isset($request->files)) {
    //         if(!$this->package->where('slug', $request->slug)->get()->first()){
    //             return redirect()->route('packages.not_found');
    //         }
            
    //         $photo = 'photo_'.$dto->image_id;
    
    //         return $package->where('slug',$dto->slug)->update([$photo=>$dto->photo]);
    
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

        // $buffet = Buffet::where('slug', $request->buffet)->get()->first();
        $packages = $this->package->where('buffet', $buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
        return view('packages.index', ['packages'=>$packages, 'buffet'=>$buffet_slug]);
    }

    public function not_found() {
        return view('package.package-not-found');
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

        return view('packages.create', ['buffet'=>$buffet_slug]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePackageRequest $request)
    {
        $slug = str_replace(' ', '-', $request->slug);
        if($this->package->where('slug', $request->slug)->get()->first()){
            return redirect()->back()->with('errors', 'Package already exists');
        }

        // if(!isset($request->images) || count($request->images) != 3) {
        //     return redirect()->back()->with('errors', 'Não existem 3 fotos na requisição');
        // }
        $buffet = Buffet::where('slug', $request->buffet)->get()->first();
        
        $package = $this->package->create([
            "name_package"=>$request->name_package,
            "food_description"=>$request->food_description,
            "beverages_description"=>$request->beverages_description,
            "status"=>$request->status ?? PackageStatus::ACTIVE->name,
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

        return redirect()->route('packages.show', $package->slug);

        
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        
        if(!$package = $this->package->where('slug', $request->package)->get()->first()){
            return redirect()->route('packages.index', $buffet_slug)->with('errors', 'Package not found');
        }

        // $package = $this->package->where('buffet', $buffet->id);

        return view('packages.show', ['package'=>$package, 'buffet'=>$buffet_slug]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        // dd($request->buffet, $request->package); 
        if (!$package = $this->package->where('slug', $request->slug)->get()->first()) {
            return redirect()->back()->with('errors', 'Package not found');
            
        }

        return view('packages.update', ['package'=> $package, 'buffet'=>$buffet_slug]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageRequest $request, Package $package)
    {
        if($this->package->where('slug', $request->slug)->get()->first()){
            return redirect()->back()->with('errors', 'Package already exists');
        }

        return redirect()->route('packages.show', $package->package);
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Package $package, Request $request)
     {
         if ($package = $this->package->where('slug', $request->slug)->get()->first()) {
             return redirect()->route('package.not_found');
         }
// 
         $package->update(['status' => 'unactive']);
// 
         return redirect()->route('package.index');
     }

    // public function change_status(Request $request)
    // {
    //     if (!$package = $this->package->where('slug', $request->slug)->get()->first()) {
    //         return redirect()->back()->with('errors', 'Package not found');
    //     }
// 
    //     $this->package->change_status($package->id);
// 
    //     return redirect()->route('packages.index');
    // }
}
