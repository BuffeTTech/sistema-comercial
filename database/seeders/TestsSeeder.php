<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\BuffetStatus;
use App\Enums\DayWeek;
use App\Enums\GuestStatus;
use App\Enums\QuestionType;
use App\Enums\RecommendationStatus;
use App\Enums\SatisfactionQuestionStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Booking;
use App\Models\Buffet;
use App\Models\BuffetPhoto;
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
use App\Models\Subscription;
use App\Models\SubscriptionConfiguration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //pacotes disponiveis 
        $pacote_basico = Subscription::create([
            "name"=>"Pacote Básico",
            "slug"=>sanitize_string("Pacote Básico"),
            "description"=>"Este é um pacote de inscrição do buffet",
            "price"=>149.90,
            "discount"=>0,
            "status"=>SubscriptionStatus::ACTIVE->name,
        ]);

        $pacote_basico_configs = SubscriptionConfiguration::create([
            "max_employees"=>5,
            "max_food_photos"=>3,
            "max_decoration_photos"=>3,
            "max_recommendations"=>3,
            "max_survey_questions"=>3, 
            "subscription_id"=>$pacote_basico->id,
        ]);

        $pacote_luxo = Subscription::create([
            "name"=>"Pacote Luxo",
            "slug"=>sanitize_string("Pacote Luxo"),
            "description"=>"Este é um pacote de inscrição do buffet",
            "price"=>200.90,
            "discount"=>0,
            "status"=>SubscriptionStatus::ACTIVE->name,
        ]);

        $pacote_luxo_configs = SubscriptionConfiguration::create([
            "max_employees"=>15,
            "max_food_photos"=>6,
            "max_decoration_photos"=>6,
            "max_recommendations"=>6,
            "max_survey_questions"=>5, 
            "subscription_id"=>$pacote_luxo->id,
        ]);


        // permissoes usuarios 
        $user_role = Role::create(['name' => $pacote_basico->slug.'.user']);
        $commercial_role = Role::create(['name' => $pacote_basico->slug.'.commercial']);
        $operational_role = Role::create(['name' => $pacote_basico->slug.'.operational']);
        $administrative_role = Role::create(['name' => $pacote_basico->slug.'.administrative']);
        $administrative = Role::create(['name' => 'Commercial-Admin']);

        $p1 = Permission::create(['name'=>'show party mode']);
        $p2 = Permission::create(['name'=>'view pendent bookings']);
        $p3 = Permission::create(['name'=>'view next bookings']);
        $p4 = Permission::create(['name'=>'list booking']);
        $p5 = Permission::create(['name'=>'show booking']);
        $p6 = Permission::create(['name'=>'create booking']);
        $p7 = Permission::create(['name'=>'update booking']);
        $p8 = Permission::create(['name'=>'cancel booking']);
        $p9 = Permission::create(['name'=>'change booking status']);
        // $p10 = Permission::create(['name'=>'create guest']);
        $p11 = Permission::create(['name'=>'change guest status']);
        $p12 = Permission::create(['name'=>'show guest']);

        $user_role->givePermissionTo($p3->name);
        $user_role->givePermissionTo($p6->name);
        $user_role->givePermissionTo($p2->name);

        $administrative_role->givePermissionTo($p1->name);
        $administrative_role->givePermissionTo($p2->name);
        $administrative_role->givePermissionTo($p3->name);
        $administrative_role->givePermissionTo($p4->name);
        $administrative_role->givePermissionTo($p5->name);
        $administrative_role->givePermissionTo($p6->name);
        $administrative_role->givePermissionTo($p7->name);
        $administrative_role->givePermissionTo($p8->name);
        $administrative_role->givePermissionTo($p9->name);
        // $administrative_role->givePermissionTo($p10->name);
        $administrative_role->givePermissionTo($p11->name);
        $administrative_role->givePermissionTo($p12->name);


        $create_survey = Permission::create(['name'=>'create survey question']);
        $show_survey = Permission::create(['name'=>'show survey question']);
        $update_survey = Permission::create(['name'=>'update survey question']);
        $delete_survey = Permission::create(['name'=>'delete survey question']);
        $list_all_survey = Permission::create(['name'=>'list all survey question']);
        $list_all_buffet_survey = Permission::create(['name'=>'list all buffet survey question']);

        $administrative_role->givePermissionTo($create_survey->id);
        $administrative_role->givePermissionTo($show_survey->id);
        $administrative_role->givePermissionTo($update_survey->id);
        $administrative_role->givePermissionTo($delete_survey->id);
        $administrative_role->givePermissionTo($list_all_survey->id);
        $administrative_role->givePermissionTo($list_all_buffet_survey->id);

        //dono dos buffets 
        $user = User::create([
            'name' => "José",
            'email' => "jose@dono.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "393.492.780-73",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => null,
        ]);
        $user->assignRole($administrative_role->name);
        $user->assignRole($administrative->name);


        // dados buffet alegria 
        $buffet_alegria_address = Address::create([
            "zipcode"=>fake()->postcode(),
            "street"=>fake()->streetName(),
            "number"=>fake()->buildingNumber(),
            "neighborhood"=>fake()->secondaryAddress(),
            "state"=>fake()->state(),
            "city"=>fake()->city(),
            "country"=>fake()->country(),
            "complement"=>""
        ]);
      
        $buffet_alegria_phone1 = Phone::create([
            'number'=>'(19) 99999-9999'
        ]);
      
        $buffet_alegria = Buffet::create([
            'trading_name' => 'Buffet Alegria',
            'email' => 'buffet@alegria.com',
            'slug' => 'buffet-alegria',
            'document' => "47.592.257/0001-43",
            'owner_id' => $user->id,
            'status' => BuffetStatus::ACTIVE->name,
            'phone1'=>$buffet_alegria_phone1->id,
            'address'=>$buffet_alegria_address->id
        ]);
      
        // $logo = BuffetPhoto::create([
        //     'file_name'=>'ddasdasdas.jpg',
        //     'file_path'=>'/ddasdasdas-.jpg',
        //     'file_extension'=>'jpg',
        //     'mime_type'=>'image/jpeg',
        //     'file_size'=>'31904',
        //     'buffet_id'=>$buffet->id
        // ]);
      
        // $buffet->update(['logo_id'=>$logo->id]);
      
        $buffet_alegria_subscription = BuffetSubscription::create([
            'buffet_id'=>$buffet_alegria->id,
            'subscription_id'=>$pacote_basico->id,
            'expires_in'=>Carbon::now()->addDays(2)
        ]);

        // dados buffet fazendinha 
        $buffet_fazendinha_address = Address::create([
            "zipcode"=>fake()->postcode(),
            "street"=>fake()->streetName(),
            "number"=>fake()->buildingNumber(),
            "neighborhood"=>fake()->secondaryAddress(),
            "state"=>fake()->state(),
            "city"=>fake()->city(),
            "country"=>fake()->country(),
            "complement"=>""
        ]);
      
        $buffet_fazendinha_phone1 = Phone::create([
            'number'=>'(19) 99999-9998'
        ]);
      
        $buffet_fazendinha = Buffet::create([
            'trading_name' => 'Buffet Fazendinha',
            'email' => 'buffet@fazendinha.com',
            'slug' => 'buffet-fazendinha',
            'document' => "89.500.215/0001-85",
            'owner_id' => $user->id,
            'status' => BuffetStatus::ACTIVE->name,
            'phone1'=>$buffet_fazendinha_phone1->id,
            'address'=>$buffet_fazendinha_address->id
        ]);
      
        // $logo = BuffetPhoto::create([
        //     'file_name'=>'ddasdasdas.jpg',
        //     'file_path'=>'/ddasdasdas-.jpg',
        //     'file_extension'=>'jpg',
        //     'mime_type'=>'image/jpeg',
        //     'file_size'=>'31904',
        //     'buffet_id'=>$buffet->id
        // ]);
      
        // $buffet->update(['logo_id'=>$logo->id]);
      
        $buffet_fazendinha_subscription = BuffetSubscription::create([
            'buffet_id'=>$buffet_fazendinha->id,
            'subscription_id'=>$pacote_luxo->id,
            'expires_in'=>Carbon::now()->addDays(2)
        ]);

        //usuarios buffet alegria
        // usuário normal alegria 
        $user_alegria_phone1 = Phone::create([
            'number'=>'(19) 99999-9988'
        ]);
        $user_alegria1 = User::create([
            'name' => "Maria",
            'email' => "maria@teste.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "269.803.080-11",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => $buffet_alegria->id,
            'phone1'=>$user_alegria_phone1->id
        ]);
        $user_alegria1->assignRole($user_role->name);

        $user_alegria_phone2 = Phone::create([
            'number'=>'(19) 89999-9988'
        ]);
        $user_alegria2 = User::create([
            'name' => "Paula",
            'email' => "paula@teste.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "317.573.220-86",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => $buffet_alegria->id,
            'phone1'=>$user_alegria_phone2->id
        ]);
        $user_alegria2->assignRole($user_role->name);

        // funcionarios buffet alegria 
        $adm_alegria_phone = Phone::create([
            'number'=>'(19) 99999-9888'
        ]);
        $adm_alegria = User::create([
            'name' => "Guilherme",
            'email' => "guilherme@teste.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "321.537.920-10",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => $buffet_alegria->id,
            'phone1'=>$adm_alegria_phone->id
        ]);
        $adm_alegria->assignRole($administrative_role->name);

        $com_alegria_phone = Phone::create([
            'number'=>'(19) 99999-8888'
        ]);
        $com_alegria = User::create([
            'name' => "Luigi",
            'email' => "luigi@teste.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "356.279.200-09",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => $buffet_alegria->id,
            'phone1'=>$com_alegria_phone->id
        ]);
        $com_alegria->assignRole($commercial_role->name);

        $ope_alegria_phone = Phone::create([
            'number'=>'(19) 99998-8888'
        ]);
        $ope_alegria = User::create([
            'name' => "Taynara",
            'email' => "=taynara@teste.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "828.244.150-37",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => $buffet_alegria->id,
            'phone1'=>$ope_alegria_phone->id
        ]);
        $ope_alegria->assignRole($operational_role->name);

        //horarios disponiveis 
        $schedule1 = Schedule::create([
            'day_week'=>DayWeek::SUNDAY->name,
            'start_time'=>'12:00',
            'duration'=>120,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $schedule2 = Schedule::create([
            'day_week'=>DayWeek::SUNDAY->name,
            'start_time'=>'19:00',
            'duration'=>240,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $schedule3 = Schedule::create([
            'day_week'=>DayWeek::MONDAY->name,
            'start_time'=>'18:00',
            'duration'=>240,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $schedule4 = Schedule::create([
            'day_week'=>DayWeek::TUESDAY->name, 
            'start_time'=>'15:10',
            'duration'=>120,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $schedule5 = Schedule::create([
            'day_week'=>DayWeek::WEDNESDAY->name,
            'start_time'=>'18:00',
            'duration'=>240,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $schedule6 = Schedule::create([
            'day_week'=>DayWeek::THURSDAY->name,
            'start_time'=>'18:00',
            'duration'=>240,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $schedule7 = Schedule::create([
            'day_week'=>DayWeek::FRIDAY->name,
            'start_time'=>'14:00',
            'duration'=>240,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $schedule8 = Schedule::create([
            'day_week'=>DayWeek::FRIDAY->name,
            'start_time'=>'19:00',
            'duration'=>240,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $schedule9 = Schedule::create([
            'day_week'=>DayWeek::SATURDAY->name,
            'start_time'=>'14:00',
            'duration'=>240,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $schedule10 = Schedule::create([
            'day_week'=>DayWeek::SATURDAY->name,
            'start_time'=>'19:00',
            'duration'=>240,
            'buffet_id'=>$buffet_alegria->id
        ]);

        //pacote de comida 
        $food_alegria1 = Food::create([
            'name_food'=>'Pacote Alegria',
            'food_description'=>'bauru, batata frita, pastel, espetinho, churros, fundoe, petit gateu, bolo da sua escolha',
            'beverages_description'=>'coca/zero, guarana, fanta, água, suco de laranja, morango, uva, maracúja',
            'status'=>'ACTIVE',
            'price'=>55,
            'slug'=>'pacote-alegria',
            'buffet_id'=>$buffet_alegria->id,
        ]);
        
            FoodPhoto::create([
                'file_name'=>'dadsadasd.jpg',
                'file_path'=>'/dadsadasd.jpg',
                'file_extension'=>'jpg',
                'mime_type'=>'image/jpeg',
                'file_size'=>'40847',
                'food_id'=>$food_alegria1->id
            ]);
            FoodPhoto::create([
                'file_name'=>'ddasdasdas.jpg',
                'file_path'=>'/ddasdasdas.jpg',
                'file_extension'=>'jpg',
                'mime_type'=>'image/jpeg',
                'file_size'=>'31904',
                'food_id'=>$food_alegria1->id
            ]);
        
        $food_alegria2 = Food::create([
                'name_food'=>'Pacote Felicidade',
                'food_description'=>'coxinha, bolinha de queijo, batata frita, pastel, churros, bolo da sua escolha',
                'beverages_description'=>'coca/zero, guarana, água, suco de laranja, uva',
                'status'=>'ACTIVE',
                'price'=>35,
                'slug'=>'pacote-felicidade',
                'buffet_id'=>$buffet_alegria->id,
            ]);
            
            FoodPhoto::create([
                'file_name'=>'dadsadasd.jpg',
                'file_path'=>'/dadsadasd.jpg',
                'file_extension'=>'jpg',
                'mime_type'=>'image/jpeg',
                'file_size'=>'40847',
                'food_id'=>$food_alegria2->id
            ]);
            FoodPhoto::create([
                'file_name'=>'ddasdasdas.jpg',
                'file_path'=>'/ddasdasdas.jpg',
                'file_extension'=>'jpg',
                'mime_type'=>'image/jpeg',
                'file_size'=>'31904',
                'food_id'=>$food_alegria2->id
            ]);
        
        $food_alegria3 = Food::create([
                'name_food'=>'Pacote Familia',
                'food_description'=>' escondidinho de carne, mesa de frios, batata frita, cachorro quente, nhoque, churros, sorvete de creme, creme de papaia, bolo da sua escolha',
                'beverages_description'=>'coca/zero, guarana, água, suco de laranja, uva, cerveja stela, coquetel de morango',
                'status'=>'ACTIVE',
                'price'=>65,
                'slug'=>'pacote-familia',
                'buffet_id'=>$buffet_alegria->id,
            ]);
            
            FoodPhoto::create([
                'file_name'=>'dadsadasd.jpg',
                'file_path'=>'/dadsadasd.jpg',
                'file_extension'=>'jpg',
                'mime_type'=>'image/jpeg',
                'file_size'=>'40847',
                'food_id'=>$food_alegria3->id
            ]);
            FoodPhoto::create([
                'file_name'=>'ddasdasdas.jpg',
                'file_path'=>'/ddasdasdas.jpg',
                'file_extension'=>'jpg',
                'mime_type'=>'image/jpeg',
                'file_size'=>'31904',
                'food_id'=>$food_alegria3->id
            ]);

        //decoracoes 
        $decoration_alegria1 = Decoration::create([
            'main_theme'=>'Marvel',
            'slug'=>'marvel',
            'description'=>'Decoração com bonecos e personagens da Marvel',
            'price'=>30,
            'status'=>'ACTIVE',
            'buffet_id'=>$buffet_alegria->id
        ]);
            DecorationPhotos::create([
                'file_name'=>'0_qdHImq1G588SB9Ii.jpg',
                'file_path'=>'/0_qdhimq1g588sb9ii.170554830898-.jpg',
                'file_extension'=>'jpg',
                'mime_type'=>'image/jpeg',
                'file_size'=>'191250',
                'decorations_id'=>$decoration_alegria1->id
            ]);
            DecorationPhotos::create([
                'file_name'=>'ordem-marvel-e1606754420868.jpg',
                'file_path'=>'/ordem-marvel-e1606754420868.17055483086-.jpg',
                'file_extension'=>'jpg',
                'mime_type'=>'image/jpeg',
                'file_size'=>'113155',
                'decorations_id'=>$decoration_alegria1->id
            ]);

        $decoration_alegria2 = Decoration::create([
                        'main_theme'=>'Minnie',
                        'slug'=>'minnie',
                        'description'=>'Decoração com bonecos e personagens da Minnie',
                        'price'=>30,
                        'status'=>'ACTIVE',
                        'buffet_id'=>$buffet_alegria->id
                    ]);
                    DecorationPhotos::create([
                        'file_name'=>'0_qdHImq1G588SB9Ii.jpg',
                        'file_path'=>'/0_qdhimq1g588sb9ii.170554830898-.jpg',
                        'file_extension'=>'jpg',
                        'mime_type'=>'image/jpeg',
                        'file_size'=>'191250',
                        'decorations_id'=>$decoration_alegria2->id
                    ]);
                    DecorationPhotos::create([
                        'file_name'=>'ordem-marvel-e1606754420868.jpg',
                        'file_path'=>'/ordem-marvel-e1606754420868.17055483086-.jpg',
                        'file_extension'=>'jpg',
                        'mime_type'=>'image/jpeg',
                        'file_size'=>'113155',
                        'decorations_id'=>$decoration_alegria2->id
                    ]);

        $decoration_alegria3 = Decoration::create([
                        'main_theme'=>'Moana',
                        'slug'=>'moana',
                        'description'=>'Decoração com bonecos e personagens da Moana',
                        'price'=>30,
                        'status'=>'ACTIVE',
                        'buffet_id'=>$buffet_alegria->id
                    ]);
                    DecorationPhotos::create([
                        'file_name'=>'0_qdHImq1G588SB9Ii.jpg',
                        'file_path'=>'/0_qdhimq1g588sb9ii.170554830898-.jpg',
                        'file_extension'=>'jpg',
                        'mime_type'=>'image/jpeg',
                        'file_size'=>'191250',
                        'decorations_id'=>$decoration_alegria3->id
                    ]);
                    DecorationPhotos::create([
                        'file_name'=>'ordem-marvel-e1606754420868.jpg',
                        'file_path'=>'/ordem-marvel-e1606754420868.17055483086-.jpg',
                        'file_extension'=>'jpg',
                        'mime_type'=>'image/jpeg',
                        'file_size'=>'113155',
                        'decorations_id'=>$decoration_alegria3->id
                    ]);
            
        // reservas 
        $booking_alegria1 = Booking::create([
            'name_birthdayperson'=>'André',
            'years_birthdayperson'=>15,
            'num_guests'=>50,
            'party_day'=>'2024-04-26',
            'buffet_id'=>$buffet_alegria->id,
            'food_id'=>$food_alegria1->id,
            'price_food'=>$food_alegria1->price,
            'decoration_id'=>$decoration_alegria1->id,
            'price_decoration'=>$decoration_alegria1->price,
            'schedule_id'=>$schedule7->id,
            'price_schedule'=>0,
            'discount'=>0,
            'status'=>BookingStatus::PENDENT->name,
            'user_id'=>$user_alegria1->id
        ]);
        $booking_alegria2 = Booking::create([
            'name_birthdayperson'=>'Luiza',
            'years_birthdayperson'=>6,
            'num_guests'=>100,
            'party_day'=>'2024-02-20',
            'buffet_id'=>$buffet_alegria->id,
            'food_id'=>$food_alegria3->id,
            'price_food'=>$food_alegria3->price,
            'decoration_id'=>$decoration_alegria3->id,
            'price_decoration'=>$decoration_alegria3->price,
            'schedule_id'=>$schedule7->id,
            'price_schedule'=>0,
            'discount'=>0,
            'status'=>BookingStatus::PENDENT->name,
            'user_id'=>$user_alegria2->id
        ]);
        
        $booking_alegria3 = Booking::create([
            'name_birthdayperson'=>'Silvia',
            'years_birthdayperson'=>10,
            'num_guests'=>70,
            'party_day'=>'2024-02-20',
            'buffet_id'=>$buffet_alegria->id,
            'food_id'=>$food_alegria2->id,
            'price_food'=>$food_alegria2->price,
            'decoration_id'=>$decoration_alegria2->id,
            'price_decoration'=>$decoration_alegria2->price,
            'schedule_id'=>$schedule4->id,
            'price_schedule'=>0,
            'discount'=>0,
            'status'=>BookingStatus::APPROVED->name,
            'user_id'=>$user_alegria2->id
        ]);

        //convidados silvia 
        Guest::create([
            'name'=> 'João',
            'document'=>'292.795.610-30',
            'age'=> 32,
            'booking_id'=>$booking_alegria3->id,
            'buffet_id'=>$buffet_alegria->id,
            'status'=>GuestStatus::CONFIRMED->name
        ]);

        Guest::create([
            'name'=> 'Hamilton',
            'document'=>'280.244.380-11',
            'age'=> 55,
            'booking_id'=>$booking_alegria3->id,
            'buffet_id'=>$buffet_alegria->id,
            'status'=>GuestStatus::PRESENT->name
        ]);

        Guest::create([
            'name'=> 'Maria Flor',
            'document'=>'000.841.410-69',
            'age'=> 6,
            'booking_id'=>$booking_alegria3->id,
            'buffet_id'=>$buffet_alegria->id,
            'status'=>GuestStatus::ABSENT->name
        ]);

        Guest::create([
            'name'=> 'Robson',
            'document'=>'030.410.060-90',
            'age'=> 40,
            'booking_id'=>$booking_alegria3->id,
            'buffet_id'=>$buffet_alegria->id,
            'status'=>GuestStatus::BLOCKED->name
        ]);

        Guest::create([
            'name'=> 'Fernanda',
            'document'=>'195.544.410-29',
            'age'=> 20,
            'booking_id'=>$booking_alegria3->id,
            'buffet_id'=>$buffet_alegria->id,
            'status'=>GuestStatus::CONFIRMED->name
        ]);

        Guest::create([
            'name'=> 'Prado',
            'document'=>'425.114.870-39',
            'age'=> 18,
            'booking_id'=>$booking_alegria3->id,
            'buffet_id'=>$buffet_alegria->id,
            'status'=>GuestStatus::PENDENT->name
        ]);

        //pesquisa de satisfacao  
        $question1 = SatisfactionQuestion::create([
            'question' => 'Qualidade da comida', 
            'status'  => true,
            'answers'  => 2,
            'question_type' => QuestionType::M->name,
            'buffet_id' => $buffet_alegria->id,
        ]);
        
        SatisfactionAnswer::create([
            "question_id"=>$question1->id,
            "booking_id"=>$booking_alegria1->id,
            "answer"=>'25%-50%'
        ]);
        SatisfactionAnswer::create([
            "question_id"=>$question1->id,
            "booking_id"=>$booking_alegria2->id,
            "answer"=>'25%-50%'
        ]);
        
        $question2 = SatisfactionQuestion::create([
            'question'=>'O atendimento da equipe atendeu às suas expectativas?',
            'status'=>true,
            'question_type'=>QuestionType::M->name,
            'answers'=>0,
            'buffet_id'=>$buffet_alegria->id
        ]);
        $question3 = SatisfactionQuestion::create([
            'question'=>'Deixe-nos saber mais sobre sua experiência. O que você achou mais notável ou o que poderia ser melhorado?',
            'status'=>true,
            'question_type'=>QuestionType::D->name,
            'answers'=>0,
            'buffet_id'=>$buffet_alegria->id
        ]);

        //recomendacoes 
        Recommendation::create([
            'content'=>'<p>🎉 Prepare-se para a festa mais divertida do ano! Estamos animados para convidar todos os pequenos a se juntarem a nós em uma celebração cheia de cores, brincadeiras e sorrisos. Não perca essa festa incrível!</p>',
            'status'=>RecommendationStatus::ACTIVE->name,
            'buffet_id'=>$buffet_alegria->id
        ]);
        Recommendation::create([
            'content'=>'<p>🎈 Seus amiguinhos estão convocados para uma festa cheia de magia e diversão! Teremos jogos, guloseimas deliciosas e, é claro, muita música para animar a pista de dança dos pequenos. Estamos ansiosos para compartilhar momentos mágicos juntos!</p>',
            'status'=>RecommendationStatus::ACTIVE->name,
            'buffet_id'=>$buffet_alegria->id
        ]);
        Recommendation::create([
            'content'=>'<p>🌟 A aventura vai começar! Estamos preparando uma festa incrível para os pequenos aventureiros. Com decoração temática, atividades emocionantes e um bolo delicioso, garantimos sorrisos do início ao fim. Esperamos por vocês!</p>',
            'status'=>RecommendationStatus::ACTIVE->name,
            'buffet_id'=>$buffet_alegria->id
        ]);
    }
}
