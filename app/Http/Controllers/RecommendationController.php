<?php

namespace App\Http\Controllers;

use App\Enums\RecommendationStatus;
use App\Models\Buffet;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(
        protected Buffet $buffet,
        protected Recommendation $recommendation
    ) 
    {}
    public function index(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        $buffet_id = $buffet->id;
        $recommendations = $this->recommendation->where('buffet',$buffet_id)->get();


        return view('recommendation.index',['buffet'=>$buffet_slug,'recommendations'=>$recommendations]);
    }

    public function create(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();


        return view('recommendation.create',['buffet'=>$buffet]);
    }

    public function store(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        $recommendation = $this->recommendation->create([
            'content' => $request->content,
            'status' => RecommendationStatus::ACTIVE->name,
            'buffet' => $buffet->id,
        ]);

        return redirect()->route('recommendation.index',['buffet'=>$buffet->slug]);

    }

    public function show(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        $recommendation = $this->recommendation->where('id',$request->recommendation)->get()->first();

        return view('recommendation.show',['buffet'=>$buffet,'recommendation'=>$recommendation]);

    }

    public function update(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        $recommendation = $this->recommendation->where('id',$request->recommendation)->update([
            'content' => $request->content
        ]);

        return redirect()->route('recommendation.index',['buffet'=>$buffet->slug]);

    }

    public function edit(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();
        
        $recommendation = $this->recommendation->where('id',$request->recommendation)->get()->first();


        return view('recommendation.update',['buffet'=>$buffet,'recommendation'=>$recommendation]);
    }

    public function destroy(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        $recommendation = $this->recommendation->where('id',$request->recommendation)->delete([
            'status'=>RecommendationStatus::UNACTIVE->name
        ]);
        return redirect()->route('recommendation.index',['buffet'=>$buffet->slug]);

    }
}
