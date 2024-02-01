<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\BuffetStatus;
use App\Enums\DayWeek;
use App\Enums\SubscriptionStatus;
use App\Enums\UserStatus;
use App\Models\Booking;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Decoration;
use App\Models\DecorationPhotos;
use App\Models\Food;
use App\Models\FoodPhoto;
use App\Models\Schedule;
use App\Models\Subscription;
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

        $p1 = Permission::create(['name'=>'show party mode']);
        $p2 = Permission::create(['name'=>'view pendent bookings']);
        $p3 = Permission::create(['name'=>'view next bookings']);
        $p4 = Permission::create(['name'=>'list booking']);
        $p5 = Permission::create(['name'=>'show booking']);
        $p6 = Permission::create(['name'=>'create booking']);
        $p7 = Permission::create(['name'=>'update booking']);
        $p8 = Permission::create(['name'=>'cancel booking']);
        $p9 = Permission::create(['name'=>'change booking status']);
        $p10 = Permission::create(['name'=>'create guest']);
        $p11 = Permission::create(['name'=>'change guest status']);
        $p12 = Permission::create(['name'=>'show guest']);
        $user_role->givePermissionTo($p3->name);
        $administrative_role->givePermissionTo($p1->name);
        $administrative_role->givePermissionTo($p2->name);
        $administrative_role->givePermissionTo($p3->name);
        $administrative_role->givePermissionTo($p4->name);
        $administrative_role->givePermissionTo($p5->name);
        $administrative_role->givePermissionTo($p6->name);
        $administrative_role->givePermissionTo($p7->name);
        $administrative_role->givePermissionTo($p8->name);
        $administrative_role->givePermissionTo($p9->name);
        $administrative_role->givePermissionTo($p10->name);
        $administrative_role->givePermissionTo($p11->name);
        $administrative_role->givePermissionTo($p12->name);
        
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

        $user1 = User::create([
            'name' => "GuilhermeX",
            'email' => "usuarioee@teste.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "269.803.080-11",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => $buffet->id,
        ]);

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
            'day_week'=>DayWeek::WEDNESDAY->name,
            'start_time'=>'19:00',
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
            'party_day'=>'2024-01-31',
            'buffet_id'=>$buffet->id,
            'food_id'=>$food->id,
            'price_food'=>$food->price,
            'decoration_id'=>$decoration->id,
            'price_decoration'=>$decoration->price,
            'schedule_id'=>$schedule6->id,
            'price_schedule'=>0,
            'discount'=>0,
            'status'=>BookingStatus::APPROVED->name,
            'user_id'=>$user1->id
        ]);
    }
}
