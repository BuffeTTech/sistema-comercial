<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\BuffetStatus;
use App\Enums\UserStatus;
use App\Enums\FoodStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\User;
use app\Models\Food;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            BuffetSeeder::class,
            UserSeeder::class,
            FoodSeeder::class, 

            TestsSeeder::class
        ]);

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
        $user1->assignRole($user_role->name);
        $user2 = User::create([
            'name' => "Andrade",
            'email' => "foguinho@teste.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'document' => "791.655.700-22",
            'document_type' => "CPF",
            'status' => UserStatus::ACTIVE->name,
            'buffet_id' => $buffet->id,
        ]);
        $user2->assignRole($commercial_role->name);

        // $user = User::create([
        //     'name' => "Guilherme",
        //     'email' => "teste@teste.com",
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'document' => "393.492.780-73",
        //     'document_type' => "CPF",
        //     'status' => UserStatus::ACTIVE->name,
        //     'buffet_id' => null,
        // ]);

        // $buffet = Buffet::create([
        //     'trading_name' => 'Buffet Alegria',
        //     'email' => 'buffet@alegria.com',
        //     'slug' => 'buffet-alegria',
        //     'document' => "47.592.257/0001-43",
        //     'owner_id' => $user->id,
        //     'status' => BuffetStatus::ACTIVE->name,
        // ]);

        // $user1 = User::create([
        //     'name' => "GuilhermeX",
        //     'email' => "usuarioee@teste.com",
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'document' => "269.803.080-11",
        //     'document_type' => "CPF",
        //     'status' => UserStatus::ACTIVE->name,
        //     'buffet_id' => $buffet->id,
        // ]);

        // $buffet = Buffet::create([
        //     'trading_name' => 'Buffet Felicidade',
        //     'email' => 'buffet@felicidade.com',
        //     'slug' => 'buffet-felicidade',
        //     'document' => "47.592.257/0001-50",
        //     'owner_id' => $user->id,
        //     'status' => BuffetStatus::ACTIVE->name,
        // ]);

        // // $buffet = Buffet::create([
        // //     'trading_name' => 'Buffet Família',
        // //     'email' => 'buffet@familia.com',
        // //     'slug' => 'buffet-familia',
        // //     'document' => "47.592.257/0001-70",
        // //     'owner_id' => $user->id,
        // //     'status' => BuffetStatus::UNACTIVE->name,
        // // ]);

        // $user = User::create([
        //     'name' => "GuilhermeX",
        //     'email' => "usuario@teste.com",
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'document' => "269.803.080-17",
        //     'document_type' => "CPF",
        //     'status' => UserStatus::ACTIVE->name,
        //     'buffet_id' => $buffet->id,
        // ]);
        

        // $user = User::create([
        //     'name' => "Luiza",
        //     'email' => "usuario2@teste.com",
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'document' => "269.803.080-48",
        //     'document_type' => "CPF",
        //     'status' => UserStatus::ACTIVE->name,
        //     'buffet_id' => $buffet->id,
        // ]);

        // $user = User::create([
        //     'name' => "Ximenes",
        //     'email' => "usuario3@teste.com",
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'document' => "269.803.080-62",
        //     'document_type' => "CPF",
        //     'status' => UserStatus::ACTIVE->name,
        //     'buffet_id' => $buffet->id,
        // ]);

        // $user = User::create([
        //     'name' => "Rafael",
        //     'email' => "usuario4@teste.com",
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'document' => "269.803.080-02",
        //     'document_type' => "CPF",
        //     'status' => UserStatus::ACTIVE->name,
        //     'buffet_id' => $buffet->id,
        // ]);

        // // $food = Food::create([
        // //     
        // //     'name_food' => "Pacote Bolo",
        // //     'food_description' => "Bolo",
        // //     'beverages_description' => "Bolo liquido",
        // //     'status' =>FoodStatus::ACTIVE->name, ,
        // //     'price' => "10",
        // //     'slug' => "pacote-bolo",
        // //     'buffet_id' => null,
        // //     
        // // ]);
        

        // // \App\Models\User::factory(10)->create();

        // // \App\Models\User::factory()->create([
        // //     'name' => 'Test User',
        // //     'email' => 'test@example.com',
        // // ]);
    }
}
