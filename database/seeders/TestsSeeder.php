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
use App\Models\Food;
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
        $user->assignRole($user_role->name);

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

        $food = Food::create([
            'name_food'=>'Fodinha',
            'food_description'=>'Fodinha',
            'beverages_description'=>'Fodinha',
            'status'=>'ACTIVE',
            'price'=>55,
            'slug'=>'fodinha',
            'buffet'=>$buffet->id,
        ]);

        $decoration = Decoration::create([
            'main_theme'=>'fodinha',
            'slug'=>'fodinha',
            'description'=>'fodinha',
            'price'=>66,
            'status'=>'ACTIVE',
            'buffet'=>$buffet->id
        ]);

        $booking = Booking::create([
            'name_birthdayperson'=>'Aniversario top',
            'years_birthdayperson'=>15,
            'num_guests'=>15,
            'party_day'=>'2024-01-26',
            'buffet_id'=>$buffet->id,
            'food_id'=>$food->id,
            'price_food'=>$food->price,
            'decoration_id'=>$decoration->id,
            'price_decoration'=>$decoration->price,
            'schedule_id'=>$schedule6->id,
            'price_schedule'=>0,
            'discount'=>0,
            'status'=>BookingStatus::APPROVED->name,
        ]);
    }
}
