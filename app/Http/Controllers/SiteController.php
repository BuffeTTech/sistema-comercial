<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Booking;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Decoration;
use App\Models\DecorationPhotos;
use App\Models\Food;
use App\Models\FoodPhoto;
use App\Models\Guest;
use App\Models\Phone;
use App\Models\Recommendation;
use App\Models\SatisfactionAnswer;
use App\Models\SatisfactionQuestion;
use App\Models\Schedule;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiteController extends Controller
{
    public function __construct(
        protected Buffet $buffet,
        protected User $user,
        protected Schedule $schedule,
        protected Booking $booking,
        protected Food $food,
        protected Decoration $decoration,
        protected FoodPhoto $food_photo,
        protected DecorationPhotos $decoration_photo,
        protected Guest $guest,
        protected Recommendation $recommendation,
        protected Address $address,
        protected Phone $phone,
        protected BuffetSubscription $buffet_subscription, 
        protected SatisfactionQuestion $survey,
        protected SatisfactionAnswer $answer,  
    )
    {
    }
    
    public function dashboard() {
        $user = auth()->user();
        if($user->isBuffet()) {
            $buffet = Buffet::find($user->buffet_id);
            return redirect()->route('booking.my_bookings', ['buffet'=>$buffet->slug]);
            // return redirect()->route('buffet.dashboard', ['buffet'=>$buffet->slug]);
        }
        if($user->isOwner()) {
            $buffets = $this->buffet->where('owner_id', $user->id)->get();
            return view('dashboard', ['buffets'=>$buffets]);
        }
        // Não tem permissão
        abort(401);
    }

    public function buffetAlegria(Request $request){
        $buffet = $this->buffet->where('slug', 'buffet-alegria')->get()->first();

        return view('buffetTest', ['buffet'=>$buffet]);
    }
    public function buffetTest(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet not found'])->withInput();
        }

        return view('buffetTest', ['buffet'=>$buffet]);
    }

    public function presentation_data(Request $request){
        $data = $request->data;
        foreach($data as $key=>$value){
            $owner = $this->user->where('email', $value['owner']['email'])->where('document', $value['owner']['document'])->where('status', UserStatus::ACTIVE->name)->get()->first();
            if(!$owner) {
                $owner = $this->user->create([
                    "name"=>$value['owner']['name'],
                    "email"=>$value['owner']['email'],
                    "document"=>$value['owner']['document'],
                    "document_type"=>$value['owner']['document_type'],
                    "email_verified_at"=>now(),
                    "status"=>$value['owner']['status'],
                    "password"=>Hash::make($value['owner']['password']),
                    "buffet_id"=>null,
                ]);
                if(isset($value['owner']['address'])) {
                    $owner_address = $this->address->create([
                        "zipcode"=>$value['owner']['address']['zipcode'],
                        "street"=>$value['owner']['address']['street'],
                        "number"=>$value['owner']['address']['number'],
                        "complement"=>$value['owner']['address']['complement'],
                        "neighborhood"=>$value['owner']['address']['neighborhood'],
                        "state"=>$value['owner']['address']['state'],
                        "city"=>$value['owner']['address']['city'],
                        "country"=>$value['owner']['address']['country'],
                    ]);
                    $owner->update(['address'=>$owner_address->id]);
                }
                if(isset($value['owner']['phones'])) {
                    $phones = [];
                    foreach($value['owner']['phones'] as $key=>$phone) {
                        $user_phone = $this->phone->create(['number'=>$phone['number']]);
                        array_push($phones, $user_phone);
                    }
                    if(isset($phones[0])){
                        $owner->update(['phone1'=>$phones[0]->id]);
                    }
                    if(isset($phones[1])){
                        $owner->update(['phone2'=>$phones[1]->id]);
                    }
                }
            }
            $buffet = $this->buffet->where('slug', $value['buffet']['slug'])->get()->first();
            if(!$buffet) {
                $buffet = $this->buffet->create([
                    "trading_name"=>$value['buffet']['trading_name'],
                    "email"=>$value['buffet']['email'],
                    "document"=>$value['buffet']['document'],
                    "slug"=>$value['buffet']['slug'],
                    "status"=>$value['buffet']['status'],
                    "owner_id"=>$owner->id
                ]);
            }

            $buffet_subscription = $this->buffet_subscription->where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
            
            if($buffet_subscription->expires_in < Carbon::now()) {
                return redirect()->back()->withErrors(['buffet'=> "Este buffet não está mais ativo."])->withInput();
            }


            $foods = [];
            if(isset($value['foods'])) {
                foreach($value['foods'] as $food) {
                    $fd = $this->food->where('slug', $food['slug'])->where('buffet_id', $buffet->id)->get()->first();
                    if(!$fd) {
                        $fd = $this->food->create([
                            "name_food"=>$food['name_food'],
                            "food_description"=>$food['food_description'],
                            "beverages_description"=>$food['beverages_description'],
                            "status"=>$food['status'],
                            "price"=>$food['price'],
                            "slug"=>$food['slug'],
                            "buffet_id"=>$buffet->id
                        ]);
                        array_push($foods, $fd);
                    }
                    $photos = [];
                    foreach($food['photos'] as $photo) {
                        $ph = $this->food_photo->create([
                            'file_name'=>$photo['file_name'],
                            'file_path'=>$photo['file_path'],
                            'file_extension'=>$photo['file_extension'],
                            'mime_type'=>$photo['mime_type'],
                            'file_size'=>$photo['file_size'],
                            'food_id'=>$fd->id
                        ]);
                        array_push($photos, $ph);
                    }
                    // array_push($fd, $photos);
                }
            }

            $decorations = [];
            if(isset($value['decorations'])) {
                foreach($value['decorations'] as $decoration) {
                    $dec = $this->decoration->where('slug', $decoration['slug'])->where('buffet_id', $buffet->id)->get()->first();
                    if(!$dec) {
                        $dec = $this->decoration->create([
                            "main_theme"=>$decoration['main_theme'],
                            "slug"=>$decoration['slug'],
                            "description"=>$decoration['description'],
                            "price"=>$decoration['price'],
                            "status"=>$decoration['status'],
                            "buffet_id"=>$buffet->id
                        ]);
                        array_push($decorations, $dec);
                    }
                    $photos = [];
                    foreach($decoration['photos'] as $photo) {
                        $ph = $this->decoration_photo->create([
                            'file_name'=>$photo['file_name'],
                            'file_path'=>$photo['file_path'],
                            'file_extension'=>$photo['file_extension'],
                            'mime_type'=>$photo['mime_type'],
                            'file_size'=>$photo['file_size'],
                            'decorations_id'=>$dec->id
                        ]);
                        array_push($photos, $ph);
                    }
                }
            }

            $schedules = [];
            if(isset($value['schedules'])) {
                foreach($value['schedules'] as $schedule) {
                    $sch = $this->schedule->where('day_week', $schedule['day_week'])->where('start_time', $schedule['start_time'])->where('duration', $schedule['duration'])->where('buffet_id', $buffet->id)->get()->first();
                    if(!$sch) {
                        $sch = $this->schedule->create([
                            'day_week'=>$schedule['day_week'],
                            'start_time'=>$schedule['start_time'],
                            'duration'=>$schedule['duration'],
                            "buffet_id"=>$buffet->id,
                            "status"=>$schedule['status']
                        ]);
                        array_push($schedules, $sch);
                    }
                }
            }

            $users = [];
            if(isset($value['users'])) {
                foreach($value['users'] as $user) {
                    $usr = $this->user->where('email', $user['user']['email'])->where('document', $user['user']['document'])->where('status', UserStatus::ACTIVE->name)->where('buffet_id', $buffet->id)->get()->first();
                    if(!$usr) {
                        $usr = $this->user->create([
                            "name"=>$user['user']['name'],
                            "email"=>$user['user']['email'],
                            "document"=>$user['user']['document'],
                            "document_type"=>$user['user']['document_type'],
                            "email_verified_at"=>now(),
                            "status"=>$user['user']['status'],
                            "password"=>Hash::make($user['user']['password']),
                            "buffet_id"=>$buffet->id,
                        ]);
                        if(isset($user['address']) && count($user['address']) !== 0) {
                            $usr_address = $this->address->create([
                                "zipcode"=>$user['address']['zipcode'],
                                "street"=>$user['address']['street'],
                                "number"=>$user['address']['number'],
                                "complement"=>$user['address']['complement'],
                                "neighborhood"=>$user['address']['neighborhood'],
                                "state"=>$user['address']['state'],
                                "city"=>$user['address']['city'],
                                "country"=>$user['address']['country'],
                            ]);
                            $usr->update(['address'=>$usr_address->id]);
                        }
    
                        if(isset($user['phones'])) {
                            $user_phones = [];
                            foreach($user['phones'] as $key=>$phone) {
                                $user_phone = $this->phone->create(['number'=>$phone['number']]);
                                array_push($user_phones, $user_phone);
                            }
                            if(isset($user_phones[0])){
                                $usr->update(['phone1'=>$user_phones[0]->id]);
                            }
                            if(isset($user_phones[1])){
                                $usr->update(['phone2'=>$user_phones[1]->id]);
                            }
                        }
    
                        $usr->assignRole($buffet_subscription->subscription->slug.'.'.$user['user']['role']);
                        array_push($users, $usr);
                    }
                }
            }

            $recommendations = [];
            if(isset($value['recommendations'])) {
                foreach($value['recommendations'] as $recommendation) {
                    $recomm = $this->recommendation->where('content', $recommendation['content'])->where('status', $recommendation['status'])->where('buffet_id', $buffet->id)->get()->first();
                    if(!$recomm) {
                        $recomm = $this->recommendation->create([
                            'content'=>$recommendation['content'],
                            'status'=>$recommendation['status'],
                            "buffet_id"=>$buffet->id
                        ]);
                        array_push($recommendations, $recomm);
                    }
                }
            }

            $survey_questions = [];
            if(isset($value['survey_questions'])) {
                foreach($value['survey_questions'] as $question) {
                    $ques = $this->survey->where('question', $question['question'])->where('status', $question['status'])->where('question_type', $question['question_type'])->where('buffet_id', $buffet->id)->get()->first();
                    if(!$ques) {
                        $ques = $this->survey->create([
                            "question"=>$question['question'],
                            "status"=>$question['status'],
                            "answers"=>$question['answers'],
                            "question_type"=>$question['question_type'],
                            "buffet_id"=>$buffet->id
                        ]);
                        array_push($survey_questions, $ques);
                    }
                }
            }

            $bookings = [];
            if(isset($value['bookings']) && count($foods) !== 0 && count($decorations) !== 0 && count($schedules) !== 0 && count($users) !== 0) {
                foreach($value['bookings'] as $booking) {
                    $bk = $this->booking
                                ->where('buffet_id', $buffet->id)
                                ->where('name_birthdayperson', $booking['name_birthdayperson'])
                                ->where('years_birthdayperson', $booking['years_birthdayperson'])
                                ->where('num_guests', $booking['num_guests'])
                                ->where('party_day', $booking['party_day'])
                                ->where('schedule_id', $schedules[$booking['schedule_id']]['id'])
                                ->get()
                                ->first();
                    if(!$bk) {
                        $bk = $this->booking->create([
                            'name_birthdayperson'=>$booking['name_birthdayperson'],
                            'years_birthdayperson'=>$booking['years_birthdayperson'],
                            'birthday_date'=>$booking['birthday_date'],
                            'external_food'=>$booking['external_food'],
                            'dietary_restrictions'=>$booking['dietary_restrictions'],
                            'external_decoration'=>$booking['external_decoration'],
                            'daytime_preference'=>$booking['daytime_preference'],
                            'additional_food_observations'=>$booking['additional_food_observations'],
                            'final_notes'=>$booking['final_notes'],
                            'num_guests'=>$booking['num_guests'],
                            'party_day'=>$booking['party_day'],
                            'food_id'=>$foods[$booking['food_id']]['id'],
                            'price_food'=>$booking['price_food'],
                            'decoration_id'=>$decorations[$booking['decoration_id']]['id'],
                            'price_decoration'=>$booking['price_decoration'],
                            'schedule_id'=>$schedules[$booking['schedule_id']]['id'],
                            'price_schedule'=>$booking['price_schedule'],
                            'discount'=>$booking['discount'],
                            'status'=>$booking['status'],
                            'user_id'=>$users[$booking['user_id']]['id'],
                            "buffet_id"=>$buffet->id,
                            'price'=>$foods[$booking['food_id']]['price'] * $booking['num_guests'] + $decorations[$booking['decoration_id']]['price'] + $booking['price_schedule']
                        ]);
                        array_push($bookings, $bk);
                    }
                    $guests = [];
                    if(isset($booking['guests'])){
                        foreach($booking['guests'] as $guest) {
                            $gues = $this->guest->where('document', $guest['document'])->where('status', $guest['status'])->where('booking_id', $bk->id)->get()->first();
                            if(!$gues) {
                                $gues = $this->guest->create([
                                    "name"=>$guest['name'],
                                    "document"=>$guest['document'],
                                    "age"=>$guest['age'],
                                    "booking_id"=>$bk->id,
                                    "buffet_id"=>$buffet->id,
                                    "status"=>$guest['status'],
                                ]);
                            }
                            array_push($guests, $gues);
                        }
                    }

                    $answers = [];
                    if(isset($booking['survey_answers']) && isset($value['survey_questions']) && $bk->status == "FINISHED") {
                        foreach($booking['survey_answers'] as $answer) {
                            $ans = $this->answer->where('question_id', $survey_questions[$answer['question_id']]['id'])->where('booking_id', $bk->id)->get()->first();
                            if(!$ans) {
                                $ans = $this->answer->create([
                                    "question_id"=>$survey_questions[$answer['question_id']]['id'],
                                    "answer"=>$answer['answer'],
                                    "booking_id"=>$bk->id
                                ]);
                            }
                            array_push($answers, $ans);
                        }
                    }
                }
            }

        }
        return response()->json(['data'=>$data]);
    }
}
