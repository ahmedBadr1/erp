<?php

namespace Database\Factories\Crm;

use App\Models\Accounting\Account;
use App\Models\System\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Crm\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statues = Status::where('type','client')->pluck('id')->toArray();
        return [
            'name' => $this->faker->name(),
            'code' => $this->faker->countryCode() .  $this->faker->randomNumber(5),
            'email' => $this->faker->email(),
            'phone' => $this->faker->e164PhoneNumber(),
            'status_id' => $statues[array_rand($statues)] ,
            'address' => $this->faker->address(),
            'account_id' => Account::all()->random()->id
        ];
    }
}
