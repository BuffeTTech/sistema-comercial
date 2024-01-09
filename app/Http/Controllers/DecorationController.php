<?php

namespace App\Http\Controllers;

use App\Enums\DecorationStatus;
use App\Http\Requests\Decorations\StoreDecorationRequest;
use App\Http\Requests\Decorations\UpdateDecorationRequest;
use App\Models\Buffet;
use App\Models\Decoration;
use App\Models\DecorationPhotos;
use Illuminate\Http\Request;

class DecorationController extends Controller
{
    public function __construct(
        protected Decoration $decoration,
        protected Buffet $buffet,
        protected DecorationPhotos $photos,
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$request->buffet)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return null;
        }

        $decorations = $this->decoration->where('buffet',$buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
        return view('decoration.index',['decorations'=>$decorations,'buffet'=>$buffet_slug],);
    }

    public function not_found() {
        return view('decoration.decoration-not-found');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$request->buffet)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return null;
        }

        return view('decoration.create', ['buffet'=>$buffet]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDecorationRequest $request)
    {
        $slug = str_replace(' ', '-', $request->slug);
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('id',$request->buffet)->get()->first();

        if($this->decoration->where('slug', $request->decoration)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->back()->withErrors(['slug' => 'decoration already exists.'])->withInput();
        }

        $decoration = $this->decoration->create([
            'main_theme'=>$request->main_theme,
            'slug'=>$slug,
            'description'=>$request->description,
            'price'=>$request->price,
            'status'=> DecorationStatus::ACTIVE->name,
            'buffet'=> $buffet->id
        ]);

        return redirect()->route('decoration.show',['buffet'=>$buffet_slug, 'decoration'=>$decoration]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $request->buffet)->get()->first();

        if(!$decoration= $this->decoration->where('slug', $request->decoration)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->route('decoration.index', $buffet_slug)->withErrors(['slug' => 'decoration not found.'])->withInput();
        }

        //$decoration = $this->decoration->where('slug',$request->decoration)->get()->first();

        return view('decoration.show',['buffet'=>$buffet_slug, 'decoration'=>$decoration]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$request->buffet)->get()->first();

        if(!$decoration= $this->decoration->where('slug', $request->decoration)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->route('decoration.index', $buffet_slug)->withErrors(['slug' => 'deoration not found.'])->withInput();
        }

        //$decoration = $this->decoration->where('slug',$request->decoration)->get()->first();
        return view('decoration.update',['buffet'=>$buffet,'decoration'=>$decoration]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDecorationRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$request->buffet)->get()->first();

        $decoration = $this->decoration->where('slug',$request->decoration)->where('buffet', $buffet->id)->get()->first();
        if(!$decoration){
            return redirect()->back()->whithErrors('slug', 'decoration not found')->withInput; 
        }

        $decoration_exists = $this->decoration->where('slug', $request->slug)->where('buffet', $buffet->id)->get()->first();
        if($decoration_exists && $decoration_exists->id !== $decoration->id){
            return redirect()->back()->withErrors(['slug' => 'decoration already exists'])->withInput(); 
        }

        $decoration->update([
            'main_theme' => $request->main_theme,
            'slug'=>$request->slug,
            'description'=>$request->description,
            'price'=>$request->price,
            'status'=> $request->status ?? DecorationStatus::ACTIVE->name,
            'buffet'=> $buffet->id
        ]);

        $dec = $this->decoration->find($decoration->id); 

        return redirect()->back(); // para ser possivel update foto e conteudos ao mesmo tempo 

    }

    /**
     * Remove the specified resource from storage.
     */

    public function change_status(Request $request)
     {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        $decoration = $this->decoration->where('slug', $request->decoration)->where('buffet', $buffet->id)->get()->first();
        if (!$decoration) {
            return redirect()->back()->withErrors(['slug' => 'decoration not found.'])->withInput();
        }

        $decoration->update(['status'=>$request->status]);

        return redirect()->route('decoration.index', ['buffet'=>$buffet_slug]);
     }
}
