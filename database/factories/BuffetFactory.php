<?php

namespace Database\Factories;

use App\Enums\BuffetStatus;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Buffet;
use App\Models\Phone;
use App\Models\User;
use Faker\Provider\pt_BR\Company;
use Faker\Provider\pt_BR\Person;
use Faker\Provider\pt_BR\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Buffet>
 */
class BuffetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new Company($this->faker));
        $this->faker->addProvider(new PhoneNumber($this->faker));
        // $users = User::where('status', UserStatus::ACTIVE->name)->where('')->pluck('id')->toArray();

        // $roleName = "representative";
        // $users = User::whereHas('roles', function ($query) use ($roleName) {
        //     $query->where('name', $roleName);
        // })->where('status', UserStatus::ACTIVE->name)->pluck('id')->toArray();
        $users = User::pluck('id')->toArray();
        
        return [
            'trading_name' => $this->faker->name(),
            'email' => fake()->unique()->safeEmail(),
            'slug' => fake()->unique()->slug(2),
            'document' => $this->faker->cnpj(),
            'owner_id' => count($users) == 0 ? function () {
                return User::factory()->create();
            } : fake()->randomElement($users),
            'status' => fake()->randomElement(array_column(BuffetStatus::cases(), 'name')),
        ];
    }
    public function configure():static
    {
        return $this->afterCreating(function(Buffet $buffet){
            $phone1 = Phone::create([
                'number'=>$this->faker->phoneNumber()
            ]);
            $phone2 = Phone::create([
                'number'=>$this->faker->phoneNumber()
            ]);
            $address = Address::create([
                "zipcode"=>fake()->postcode(),
                "street"=>fake()->streetName(),
                "number"=>fake()->buildingNumber(),
                "neighborhood"=>fake()->secondaryAddress(),
                "state"=>fake()->state(),
                "city"=>fake()->city(),
                "country"=>fake()->country(),
                "complement"=>""
            ]);
            $buffet->update([
                'phone1'=>$phone1->id,
                'phone2'=>$phone2->id,
                'address'=>$address->id,
            ]);
        });
    }
}