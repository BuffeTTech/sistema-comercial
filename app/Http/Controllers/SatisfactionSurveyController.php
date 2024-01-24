<?php

namespace App\Http\Controllers;

use App\Enums\QuestionType;
use App\Enums\SatisfactionQuestionStatus;
use Illuminate\Http\Request;
use App\Models\Booking; 
use App\Models\Buffet; 
use App\Htt\Request\SatisfactionSurvey\StoreSatisfactionQuestionRequest; 
use App\Htt\Request\SatisfactionSurvey\StoreSatisfactionAnswerRequest; 
use App\Htt\Request\SatisfactionSurvey\UpdateSatisfactionQuestionRequest; 
use App\Htt\Request\SatisfactionSurvey\UpdateSatisfactionAnswerRequest; 

class SatisfactionSurvey extends Controller
{
    public function __construct(
        protected SatisfactionSurvey $survey,
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

        $questions = $this->survey->where('buffet_id', $buffet->id)->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1));

        return view('satisfactionsurvey.index',['buffet'=>$buffet, 'questions'=>$questions]); 
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
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 

        if($this->survey->where('id', $request->survey)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->back()->withErrors(['id' => 'question already exists.'])->withInput();
        }

        $question = $this->survey->create([
            "question"=>$request->question, 
            "status"=>$request->status ?? SatisfactionQuestionStatus::ACTIVE->name, 
            "question_type"=>$request->question_type ?? QuestionType::D->name, 
            "buffet_id"=>$buffet->id
        ]);

        return redirect()->route('survey.show', ['buffet'=>$buffet, 'question'=>$question]);
    }

    public function show(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 

        $question = $this->survey->where('id', $request->survey)->where('buffet', $buffet->id)->get()->first(); 
        if(!$question){
            return redirect()->route('survey.index', $buffet_slug)->withErrors(['id' => 'question not found'])->withInput();
        }

        return view('survey .show', ['buffet'=>$buffet, 'question'=>$question]);
    }
    public function edit(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        
        if (!$question = $this->survey->where('id', $request->survey)->where('buffet', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['id' => 'question not found.'])->withInput();
            
        }

        return view('survey .update', ['buffet'=>$buffet, 'question'=> $question ]);
    }

    public function update(){

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
