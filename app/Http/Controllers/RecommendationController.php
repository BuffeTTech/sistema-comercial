<?php

namespace App\Http\Controllers;

use App\Enums\RecommendationStatus;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Recommendation;
use App\Models\SubscriptionConfiguration;
use App\Models\User;
use Carbon\Carbon;
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

        $recommendations = $this->recommendation->where('buffet_id',$buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));;

        $this->authorize('viewAny', [Recommendation::class, $buffet]);

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        $total = $this->recommendation->where('buffet_id',$buffet->id)->where('status', RecommendationStatus::ACTIVE->name)->get();
        return view('recommendation.index',['buffet'=>$buffet,'recommendations'=>$recommendations, 'configurations'=>$configurations, 'total'=>count($total)]);
    }

    public function create(Request $request){
        $buffet_slug = $request->buffet;

        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $this->authorize('create', [Recommendation::class, $buffet]);

        $recommendations = $this->recommendation->where('buffet_id',$buffet->id)->where('status', RecommendationStatus::ACTIVE->name)->get();

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['generic_error'=> "Buffet is not active"])->withInput();
        }
        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        if(count($recommendations) >= $configurations['max_recommendations']) {
            return redirect()->back()->withErrors(['generic_error'=> 'Não é permitido cadastrar mais recomendações neste plano.'])->withInput();
        }

        return view('recommendation.create',['buffet'=>$buffet]);
    }

    public function store(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug',$buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $this->authorize('create', [Recommendation::class, $buffet]);

        $recommendations = $this->recommendation->where('buffet_id',$buffet->id)->where('status', RecommendationStatus::ACTIVE->name)->get();

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['generic_error'=> "Buffet is not active"])->withInput();
        }
        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        if(count($recommendations) >= $configurations['max_recommendations']) {
            return redirect()->back()->withErrors(['generic_error'=> 'Não é permitido cadastrar mais recomendações neste plano.'])->withInput();
        }
        
        $recommendation = $this->recommendation->create([
            'content' => $request->content,
            'status' => RecommendationStatus::ACTIVE->name,
            'buffet_id' => $buffet->id,
        ]);

        return redirect()->route('recommendation.show',['buffet'=>$buffet->slug, 'recommendation'=>$recommendation->hashed_id])->with(['success'=>'Recomendação criada com sucesso!']);

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

        $this->authorize('view', [Recommendation::class,$recommendation, $buffet]);

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
        $this->authorize('update', [Recommendation::class, $recommendation, $buffet]);

        $recommendation->update([
            'content' => $request->content
        ]);

        return redirect()->route('recommendation.edit', ['buffet'=>$buffet->slug, 'recommendation'=>$recommendation->hashed_id])->with(['success'=>'Recomendação atualizada com sucesso!']);

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

        $this->authorize('update', [Recommendation::class,$recommendation, $buffet]);

        return view('recommendation.update',['buffet'=>$buffet,'recommendation'=>$recommendation])->with(['success'=>'Recomendação atualizada com sucesso!']);
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
        
        $this->authorize('delete', [Recommendation::class,$recommendation, $buffet]);

        $recommendation->update([
            'status'=>RecommendationStatus::UNACTIVE->name
        ]);

        return redirect()->back()->with(['success'=>'Recomendação inativada com sucesso!']);

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
        
        $this->authorize('change_status', [Recommendation::class,$recommendation, $buffet]);

        $recommendations = $this->recommendation->where('buffet_id',$buffet->id)->where('status', RecommendationStatus::ACTIVE->name)->get();

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['generic_error'=> "Buffet is not active"])->withInput();
        }
        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        if(count($recommendations) >= $configurations['max_recommendations']) {
            return redirect()->back()->withErrors(['generic_error'=> 'Não é permitido ativar mais recomendações neste plano.'])->withInput();
        }

        $recommendation->update([
            'status'=>$request->status
        ]);

        return redirect()->back()->with(['success'=>'Status da recomendação atualizado com sucesso!']);
    }
}