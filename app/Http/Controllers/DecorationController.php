<?php

namespace App\Http\Controllers;

use App\Enums\DecorationStatus;
use App\Http\Requests\StoreDecorationRequest;
use App\Http\Requests\UpdateDecorationRequest;
use App\Models\Buffet;
use App\Models\Decoration;
use Illuminate\Http\Request;

class DecorationController extends Controller
{
    public function __construct(
        protected Decoration $decoration,
        protected Buffet $buffet
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buffet = $this->buffet->where('slug',$request->buffet)->get()->first();
        $decorations = $this->decoration->where('buffet_id',$buffet->id)->get();
        return view('decoration.index',['decorations'=>$decorations,'buffet'=>$buffet],);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $buffet = $this->buffet->where('slug',$request->buffet)->get()->first();

        return view('decoration.create', ['buffet'=>$buffet]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDecorationRequest $request)
    {
        $buffet = $this->buffet->where('slug',$request->buffet)->get()->first();

        $decoration = $this->decoration->create([
            'main_theme'=>$request->main_theme,
            'slug'=>$request->slug,
            'description'=>$request->description,
            'price'=>$request->price,
            'status'=> DecorationStatus::ACTIVE->name,
            'buffet_id'=> $buffet->id
        ]);
        return redirect()->route('decoration.index',['buffet'=>$buffet->slug]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Decoration $decoration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Decoration $decoration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDecorationRequest $request, Decoration $decoration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Decoration $decoration)
    {
        //
    }
}
