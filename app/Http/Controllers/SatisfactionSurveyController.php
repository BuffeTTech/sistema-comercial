<?php

namespace App\Http\Controllers;

use App\Enums\QuestionType;
use App\Enums\SatisfactionQuestionStatus;
use Illuminate\Http\Request;
use App\Models\SatisfactionQuestion; 
use App\Models\SatisfactionAnswer; 
use App\Models\Booking; 
use App\Models\Buffet; 
use App\Http\Requests\SatisfactionSurvey\StoreSatisfactionQuestionRequest; 
use App\Http\Requests\SatisfactionSurvey\StoreSatisfactionAnswerRequest; 
use App\Http\Requests\SatisfactionSurvey\UpdateSatisfactionQuestionRequest; 
use App\Http\Requests\SatisfactionSurvey\UpdateSatisfactionAnswerRequest; 
use App\Models\BuffetSubscription;
use Carbon\Carbon;

class SatisfactionSurveyController extends Controller
{
    public function __construct(
        protected SatisfactionQuestion $survey,
        protected SatisfactionAnswer $answer,  
        protected Booking $booking, 
        protected Buffet $buffet, 
    ){
        // $this->hashids = new Hashids(config('app.name'));
    }

    public function index(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 

        if(!$buffet || !$buffet_slug){
            return null; 
        }

        $surveys = $this->survey->where('buffet_id', $buffet->id)->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1));

        return view('survey.index',['buffet'=>$buffet, 'surveys'=>$surveys]); 
    }

    public function create(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 

        if(!$buffet || !$buffet_slug){
            return redirect()->back()->withErrors(['buffet'=>'buffet not found'])->withInput();
        }

        return view('survey.create', ['buffet'=>$buffet]);
    }

    public function store(StoreSatisfactionQuestionRequest $request){
        // não esta chegando no store depois do create 
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 

        if($survey = $this->survey->where('id', $request->survey)->where('buffet_id', $buffet->id)->get()->first()){
            return redirect()->back()->withErrors(['id' => 'question already exists.'])->withInput();
        }

        $survey = $this->survey->create([
            "question"=>$request->question, 
            "status"=>$request->status ?? SatisfactionQuestionStatus::ACTIVE->name, 
            "question_type"=>$request->question_type ?? QuestionType::D->name, 
            "buffet_id"=>$buffet->id
        ]);

        return redirect()->route('survey.show', ['buffet'=>$buffet_slug, 'survey'=>$survey->id]);
    }

    public function show(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 

        $survey = $this->survey->where('id', $request->survey)->where('buffet_id', $buffet->id)->with('user_answers')->get()->first();
        if(!$survey){
            return redirect()->route('survey.index', $buffet_slug)->withErrors(['id' => 'question not found'])->withInput();
        }

        return view('survey.show', ['buffet'=>$buffet, 'survey'=>$survey]);
    }
    public function edit(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        
        if (!$survey = $this->survey->where('id', $request->survey)->where('buffet_id', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['id' => 'question not found.'])->withInput();   
        }

        return view('survey.update', ['buffet'=>$buffet, 'survey'=> $survey ]);
    }

    public function update(UpdateSatisfactionQuestionRequest $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        $survey = $this->survey->where('id', $request->survey)->where('buffet_id', $buffet->id)->get()->first();
        if(!$survey){
            return redirect()->back()->withErrors(['slug' => 'question not found.'])->withInput();
        }

        $survey_exists = $this->survey->where('id', $request->slug)->where('buffet_id', $buffet->id)->get()->first();
        if($survey_exists && $survey_exists->id !== $survey->id) {
            return redirect()->back()->withErrors(['slug' => 'question already exists.'])->withInput();
        }

        $survey->update([
            "question"=>$request->question, 
            "status"=>$request->status ?? SatisfactionQuestionStatus::ACTIVE->name, 
            "question_type"=>$request->question_type ?? QuestionType::D->name, 
            "buffet_id"=>$buffet->id
        ]);

        return redirect()->route('survey.show', ['buffet'=>$buffet_slug, 'survey'=>$survey->id]);

    }

    public function delete(){

    }

    public function change_question_status(){

    }

    public function find_question(){

    }

    public function get_question_by_user_id(){

    }

    public function answer(){

    }
}
