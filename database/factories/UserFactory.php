<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Buffet;
use App\Models\Commercial;
use App\Models\Phone;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Faker\Generator as Faker;
use Faker\Provider\pt_BR\Person;
use Faker\Provider\pt_BR\PhoneNumber;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new PhoneNumber($this->faker));
        $buffets = Buffet::pluck('id')->toArray();
        return [
            'name' => $this->faker->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'document' => $this->faker->cpf(),
            'document_type' => DocumentType::CPF->name,
            'status' => fake()->randomElement(array_column(UserStatus::cases(), 'name')),
            'remember_token' => Str::random(10),
            'buffet_id' => fake()->randomElement($buffets),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            //$roles = ['commercial', 'representative', 'administrative'];
            //$role_chosed = fake()->randomElement($roles);

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

            $user->update([
                'phone1'=>$phone1->id,
                'phone2'=>$phone2->id,
                'address'=>$address->id,
            ]);

            //if($role_chosed == "representative") {
            //    Representative::create(['user_id'=>$user->id]);                
            //} else if($role_chosed == "commercial") {
            //    Commercial::create(['user_id'=>$user->id]);
            //}
//
            //$user->assignRole($role_chosed);
        });
    }


    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
