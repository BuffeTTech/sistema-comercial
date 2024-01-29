<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\BuffetStatus;
use App\Enums\DayWeek;
use App\Enums\QuestionType;
use App\Enums\SatisfactionQuestionStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\UserStatus;
use App\Models\Booking;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Decoration;
use App\Models\DecorationPhotos;
use App\Models\Food;
use App\Models\FoodPhoto;
use App\Models\Phone;
use App\Models\SatisfactionQuestion;
use App\Models\Schedule;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pacote_alegria = Subscription::create([
            "name"=>"Pacote Alegria",
            "slug"=>sanitize_string("Pacote Alegria"),
            "description"=>"Este é um pacote de inscrição do buffet",
            "price"=>159.99,
            "discount"=>0,
            "status"=>SubscriptionStatus::ACTIVE->name,
        ]);
        $user_role = Role::create(['name' => $pacote_alegria->slug.'.user']);
        $commercial_role = Role::create(['name' => $pacote_alegria->slug.'.commercial']);
        $operational_role = Role::create(['name' => $pacote_alegria->slug.'.operational']);
        $administrative_role = Role::create(['name' => $pacote_alegria->slug.'.administrative']);
        
        $user = User::create([
            'name' => "Guilherme",
            'email' => "teste@teste.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "393.492.780-73",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => null,
        ]);
        $user->assignRole($administrative_role->name);

        $buffet = Buffet::create([
            'trading_name' => 'Buffet Alegria',
            'email' => 'buffet@alegria.com',
            'slug' => 'buffet-alegria',
            'document' => "47.592.257/0001-43",
            'owner_id' => $user->id,
            'status' => BuffetStatus::ACTIVE->name,
        ]);

        $buffet_subscription = BuffetSubscription::create([
            'buffet_id'=>$buffet->id,
            'subscription_id'=>$pacote_alegria->id,
            'expires_in'=>Carbon::now()->addDays(2)
        ]);

        $user1_phone = Phone::create([
            'number'=>'(19) 99999-9999'
        ]);
        $user1 = User::create([
            'name' => "GuilhermeX",
            'email' => "usuarioee@teste.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "269.803.080-11",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => $buffet->id,
            'phone1'=>$user1_phone->id
        ]);
        $user1->assignRole($operational_role->name);

        $schedule1 = Schedule::create([
            'day_week'=>DayWeek::SUNDAY->name,
            'start_time'=>'12:00',
            'duration'=>120,
            'buffet_id'=>$buffet->id
        ]);
        $schedule2 = Schedule::create([
            'day_week'=>DayWeek::MONDAY->name,
            'start_time'=>'17:00',
            'duration'=>120,
            'buffet_id'=>$buffet->id
        ]);
        $schedule3 = Schedule::create([
            'day_week'=>DayWeek::MONDAY->name,
            'start_time'=>'20:00',
            'duration'=>120,
            'buffet_id'=>$buffet->id
        ]);
        $schedule4 = Schedule::create([
            'day_week'=>DayWeek::FRIDAY->name,
            'start_time'=>'11:00',
            'duration'=>120,
            'buffet_id'=>$buffet->id
        ]);
        $schedule5 = Schedule::create([
            'day_week'=>DayWeek::FRIDAY->name,
            'start_time'=>'15:00',
            'duration'=>120,
            'buffet_id'=>$buffet->id
        ]);
        $schedule6 = Schedule::create([
            'day_week'=>DayWeek::FRIDAY->name,
            'start_time'=>'18:00',
            'duration'=>120,
            'buffet_id'=>$buffet->id
        ]);
        $schedule7 = Schedule::create([
            'day_week'=>DayWeek::SATURDAY->name,
            'start_time'=>'14:00',
            'duration'=>120,
            'buffet_id'=>$buffet->id
        ]);
        $schedule8 = Schedule::create([
            'day_week'=>DayWeek::SATURDAY->name,
            'start_time'=>'17:00',
            'duration'=>120,
            'buffet_id'=>$buffet->id
        ]);
        $schedule9 = Schedule::create([
            'day_week'=>DayWeek::SATURDAY->name,
            'start_time'=>'21:00',
            'duration'=>120,
            'buffet_id'=>$buffet->id
        ]);
        $schedule10 = Schedule::create([
            'day_week'=>DayWeek::THURSDAY->name,
            'start_time'=>'19:00',
            'duration'=>150,
            'buffet_id'=>$buffet->id
        ]);

        $food = Food::create([
            'name_food'=>'Pacote Alegria',
            'food_description'=>'bauru, batata frita, pastel, espetinho, churros, fundoe, petit gateu, bolo da sua escolha',
            'beverages_description'=>'coca/zero, guarana, fanta, água, suco de laranja, morango, uva, maracúja',
            'status'=>'ACTIVE',
            'price'=>55,
            'slug'=>'pacote-alegria',
            'buffet'=>$buffet->id,
        ]);

        FoodPhoto::create([
            'file_name'=>'169998221766.jpg',
            'file_path'=>'/169998221766.170554800078-.jpg',
            'file_extension'=>'jpg',
            'mime_type'=>'image/jpeg',
            'file_size'=>'40847',
            'food'=>$food->id
        ]);
        FoodPhoto::create([
            'file_name'=>'169998221749.jpg',
            'file_path'=>'/169998221749.170554800088-.jpg',
            'file_extension'=>'jpg',
            'mime_type'=>'image/jpeg',
            'file_size'=>'31904',
            'food'=>$food->id
        ]);

        $decoration = Decoration::create([
            'main_theme'=>'Marvel',
            'slug'=>'marvel',
            'description'=>'Decoração com bonecos e personagens da Marvel',
            'price'=>66,
            'status'=>'ACTIVE',
            'buffet'=>$buffet->id
        ]);
        DecorationPhotos::create([
            'file_name'=>'0_qdHImq1G588SB9Ii.jpg',
            'file_path'=>'/0_qdhimq1g588sb9ii.170554830898-.jpg',
            'file_extension'=>'jpg',
            'mime_type'=>'image/jpeg',
            'file_size'=>'191250',
            'decorations'=>$decoration->id
        ]);
        DecorationPhotos::create([
            'file_name'=>'ordem-marvel-e1606754420868.jpg',
            'file_path'=>'/ordem-marvel-e1606754420868.17055483086-.jpg',
            'file_extension'=>'jpg',
            'mime_type'=>'image/jpeg',
            'file_size'=>'113155',
            'decorations'=>$decoration->id
        ]);

        $booking = Booking::create([
            'name_birthdayperson'=>'Aniversario top',
            'years_birthdayperson'=>15,
            'num_guests'=>15,
            'party_day'=>'2024-01-18',
            'buffet_id'=>$buffet->id,
            'food_id'=>$food->id,
            'price_food'=>$food->price,
            'decoration_id'=>$decoration->id,
            'price_decoration'=>$decoration->price,
            'schedule_id'=>$schedule10->id,
            'price_schedule'=>0,
            'discount'=>0,
            'status'=>BookingStatus::FINISHED->name,
            'user_id'=>$user1->id
        ]);

        $question1 = SatisfactionQuestion::create([
            'question' => 'Qualidade da comida', 
            'status'  => true,
            'answers'  => 0,
            'question_type' => QuestionType::M->name,
            'buffet_id' => $buffet->id,
        ]);

        $question2 = SatisfactionQuestion::create([
            'question'=>'O atendimento da equipe atendeu às suas expectativas?',
            'status'=>true,
            'question_type'=>QuestionType::M->name,
            'answers'=>0,
            'buffet_id'=>$buffet->id
        ]);
        $question3 = SatisfactionQuestion::create([
            'question'=>'Deixe-nos saber mais sobre sua experiência. O que você achou mais notável ou o que poderia ser melhorado?',
            'status'=>true,
            'question_type'=>QuestionType::D->name,
            'answers'=>0,
            'buffet_id'=>$buffet->id
        ]);
        $question4 = SatisfactionQuestion::create([
            'question'=>'O quanto recomendaria este evento para amigos e familiares',
            'status'=>true,
            'question_type'=>QuestionType::M->name,
            'answers'=>0,
            'buffet_id'=>$buffet->id
        ]);
        $question5 = SatisfactionQuestion::create([
            'question'=>'Como você classificaria a variedade de opções de alimentação durante o evento?',
            'status'=>true,
            'question_type'=>QuestionType::M->name,
            'answers'=>0,
            'buffet_id'=>$buffet->id
        ]);

    }
}
