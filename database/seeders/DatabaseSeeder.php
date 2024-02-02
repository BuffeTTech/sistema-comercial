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
    }
}
