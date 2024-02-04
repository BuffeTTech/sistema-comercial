<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\QuestionType;
use App\Enums\ScheduleStatus;
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
use Hashids\Hashids;

class SatisfactionSurveyController extends Controller
{
    protected Hashids $hashids;
    
    public function __construct(
        protected SatisfactionQuestion $survey,
        protected SatisfactionAnswer $answer,  
        protected Booking $booking, 
        protected Buffet $buffet, 
    ){
        $this->hashids = new Hashids(config('app.name'));
    }

    public function index(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $this->authorize('viewAnyBuffet', [SatisfactionQuestion::class, $buffet]);  

        $surveys = $this->survey->where('buffet_id', $buffet->id)->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1));

        return view('survey.index',['buffet'=>$buffet, 'surveys'=>$surveys]); 
    }

    public function create(Request $request){
        
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        $this->authorize('create', [SatisfactionQuestion::class, $buffet]);        

        return view('survey.create', ['buffet'=>$buffet]);
    }

    public function store(StoreSatisfactionQuestionRequest $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $this->authorize('create', [SatisfactionQuestion::class,$buffet]);  

        $survey = $this->survey->create([
            "question"=>$request->question, 
            "status"=>$request->status ?? true, 
            "question_type"=>$request->question_type ?? QuestionType::D->name, 
            "buffet_id"=>$buffet->id
        ]);

        return redirect()->route('survey.show', ['buffet'=>$buffet_slug, 'survey'=>$survey->hashed_id]);
    }

    public function show(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $survey_id = $this->hashids->decode($request->survey)[0];

        $survey = $this->survey->where('id', $survey_id)->where('buffet_id', $buffet->id)->with('user_answers')->get()->first();
        if(!$survey){
            return redirect()->route('survey.index', $buffet_slug)->withErrors(['id' => 'question not found'])->withInput();
        }

        $this->authorize('view', [$survey, $buffet]);

        return view('survey.show', ['buffet'=>$buffet, 'survey'=>$survey]);
    }
    public function edit(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        $survey_id = $this->hashids->decode($request->survey)[0];
        if (!$survey = $this->survey->where('id', $survey_id)->where('buffet_id', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['id' => 'question not found.'])->withInput();   
        }

        $this->authorize('update', [SatisfactionQuestion::class, $survey, $buffet]);  

        return view('survey.update', ['buffet'=>$buffet, 'survey'=> $survey ]);
    }

    public function update(UpdateSatisfactionQuestionRequest $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        $survey_id = $this->hashids->decode($request->survey)[0];

        $survey = $this->survey->where('id', $survey_id)->where('buffet_id', $buffet->id)->get()->first();
        if(!$survey){
            return redirect()->back()->withErrors(['message' => 'question not found.'])->withInput();
        }

        $this->authorize('update', [SatisfactionQuestion::class, $survey, $buffet]);  

        $survey->update([
            "question"=>$request->question, 
            "status"=>$request->status ?? true, 
            "question_type"=>$request->question_type ?? QuestionType::D->name, 
            "buffet_id"=>$buffet->id
        ]);

        return redirect()->route('survey.show', ['buffet'=>$buffet_slug, 'survey'=>$survey->hashed_id]);

    }

    public function destroy(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $survey_id = $this->hashids->decode($request->survey)[0];
        $survey = $this->survey->where('id',$survey_id)->update(['status'=> false]);

        if(!$survey){
            return redirect()->back()->withErrors(['message' => 'question not found.'])->withInput();
        }

        return redirect()->route('survey.index',['buffet'=>$buffet_slug]);

    }

    public function change_question_status(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $survey_id = $this->hashids->decode($request->survey)[0];
        $survey = $this->survey->where('id',$survey_id)->update(['status'=> true]);

        if(!$survey){
            return redirect()->back()->withErrors(['message' => 'question not found.'])->withInput();
        }

        return redirect()->route('survey.index',['buffet'=>$buffet_slug]);

    }

    public function find_question(){

    }
    
    public function answer_question(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        // $rows = [];
        foreach ($request->rows as $question=>$answer) {
            $data = [
                "booking_id" => $request->booking_id,
                "question_id" => explode('q-',$question)[1],
                "answer" => $answer,
            ];

            if (!$q = $this->survey->where('id', $data['question_id'])->get()->first()) {
                return null;
            }
            $q->update(['answers'=>$q->answers+1]);
            $this->answer->create([
                'question_id'=>$data['question_id'],
                'booking_id'=>$data['booking_id'],
                'answer'=>$data['answer'],
            ]);

            // array_push($rows, $data);
        }
        $booking = $this->booking
            ->where('id', $request->booking_id)
            ->where('buffet_id', $buffet->id)
            ->get()
            ->first();
        $booking->update(['status'=>BookingStatus::CLOSED->name]);  

        return redirect()->back()->with(['message'=>'Pesquisa de satisfação salva com sucesso   ']);
    }

    public function api_get_question_by_user_id(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return response()->json(['message' => 'Buffet não encontrado'], 422);
        }

        $user_id = $this->hashids->decode($request->user_id)[0];

        $booking = $this->booking
                            ->where('buffet_id', $buffet->id)
                            ->where('user_id', $user_id)
                            ->where('status', BookingStatus::FINISHED->name)
                            ->get()
                            ->first();

        if(!$booking) {
            return response()->json(['message' => 'Nenhuma pesquisa de satisfação pendente'], 200);
        }

        $questions = $this->survey->inRandomOrder()->where('status', true)->get()->take(10)->toArray();

        return response()->json(['questions'=>$questions, 'data'=>['booking'=>$booking]]);
    }
}
