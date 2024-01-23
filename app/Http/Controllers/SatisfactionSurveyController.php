<?php

namespace App\Http\Controllers;

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
    ){}

    public function index(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->first(); 

        if(!$buffet || !$buffet_slug){
            return null; 
        }

        $questions = $this->survey->where('buffet_id', $buffet->id)->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1));

        return view('satisfactionsurvey.index',['buffet'=>$buffet, 'questions'=>$questions]); 
    }
}
