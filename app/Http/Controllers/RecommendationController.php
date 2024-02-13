<?php

namespace App\Http\Controllers;

use App\Enums\RecommendationStatus;
use App\Models\Buffet;
use App\Models\Recommendation;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    protected Hashids $hashids;
    public function __construct(
        protected Buffet $buffet,
        protected Recommendation $recommendation
    ) 
    {
        $this->hashids = new Hashids(config('app.name'));
    }

    public function index(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $recommendations = $this->recommendation->where('buffet_id',$buffet->id)->get();

        $this->authorize('viewAny', [User::class, $buffet]);


        return view('recommendation.index',['buffet'=>$buffet,'recommendations'=>$recommendations]);
    }

    public function create(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $this->authorize('create', [User::class, $buffet]);

        return view('recommendation.create',['buffet'=>$buffet]);
    }

    public function store(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $this->authorize('create', [User::class, $buffet]);
        
        $recommendation = $this->recommendation->create([
            'content' => $request->content,
            'status' => RecommendationStatus::ACTIVE->name,
            'buffet_id' => $buffet->id,
        ]);

        return redirect()->route('recommendation.show',['buffet'=>$buffet->slug, 'recommendation'=>$recommendation->hashed_id]);

    }

    public function show(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $recommendation_id = $this->hashids->decode($request->recommendation);
        if(!$recommendation_id) {
            return redirect()->back()->withErrors(['message'=>'Recomendação não encontrada'])->withInput();
        }
        
        $recommendation_id = $recommendation_id[0];
        $recommendation = $this->recommendation->where('id',$recommendation_id)->where('buffet_id', $buffet->id)->get()->first();

        if(!$recommendation){
            return redirect()->back()->withErrors(['message'=>'Recomendação não encontrada'])->withInput();
        }

        $this->authorize('view', [User::class,$recommendation, $buffet]);

        return view('recommendation.show',['buffet'=>$buffet,'recommendation'=>$recommendation]);

    }

    public function update(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $recommendation_id = $this->hashids->decode($request->recommendation);
        if(!$recommendation_id) {
            return redirect()->back()->withErrors(['message'=>'Recomendação não encontrada'])->withInput();
        }
        
        $recommendation_id = $recommendation_id[0];
        $recommendation = $this->recommendation->where('id',$recommendation_id)->where('buffet_id', $buffet->id)->get()->first();

        if(!$recommendation){
            return redirect()->back()->withErrors(['message'=>'Recomendação não encontrada'])->withInput();
        }
        $this->authorize('update', [User::class, $recommendation, $buffet]);

        $recommendation->update([
            'content' => $request->content
        ]);

        return redirect()->route('recommendation.index',['buffet'=>$buffet->slug]);

    }

    public function edit(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        $recommendation_id = $this->hashids->decode($request->recommendation);
        if(!$recommendation_id) {
            return redirect()->back()->withErrors(['message'=>'Recomendação não encontrada'])->withInput();
        }
        
        $recommendation_id = $recommendation_id[0];
        $recommendation = $this->recommendation->where('id',$recommendation_id)->where('buffet_id', $buffet->id)->get()->first();
        
        if(!$recommendation){
            return redirect()->back()->withErrors(['message'=>'Recomendação não encontrada'])->withInput();
        }

        $this->authorize('update', [User::class,$recommendation, $buffet]);

        return view('recommendation.update',['buffet'=>$buffet,'recommendation'=>$recommendation]);
    }

    public function destroy(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $recommendation_id = $this->hashids->decode($request->recommendation);
        if(!$recommendation_id) {
            return redirect()->back()->withErrors(['message'=>'Recomendação não encontrada'])->withInput();
        }
        
        $recommendation_id = $recommendation_id[0];
        $recommendation = $this->recommendation->where('id',$recommendation_id)->where('buffet_id', $buffet->id)->get()->first();
        
        if(!$recommendation){
            return redirect()->back()->withErrors(['message'=>'Recommendation não validada'])->withInput();
        }
        
        $this->authorize('delete', [User::class,$recommendation, $buffet]);

        $recommendation->update([
            'status'=>RecommendationStatus::UNACTIVE->name
        ]);

        return redirect()->route('recommendation.index',['buffet'=>$buffet->slug]);

    }

    public function change_status(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $recommendation_id = $this->hashids->decode($request->recommendation);
        if(!$recommendation_id) {
            return redirect()->back()->withErrors(['message'=>'Recomendação não encontrada'])->withInput();
        }
        
        $recommendation_id = $recommendation_id[0];
        $recommendation = $this->recommendation->where('id',$recommendation_id)->where('buffet_id', $buffet->id)->get()->first();
        
        if(!$recommendation){
            return redirect()->back()->withErrors(['message'=>'Recommendation não validada'])->withInput();
        }
        
        $this->authorize('change_status', [User::class,$recommendation, $buffet]);

        $recommendation->update([
            'status'=>$request->status
        ]);

        return redirect()->route('recommendation.index',['buffet'=>$buffet->slug]);
    }
}