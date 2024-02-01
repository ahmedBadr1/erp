<?php

namespace Database\Factories\Sales;

use App\Models\Accounting\Account;
use App\Models\Sales\Client;
use App\Models\System\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statues = Status::where('type','client')->pluck('id')->toArray();
        return [
            'name' => $this->faker->name(),
            'code' => $this->faker->countryCode() .  $this->faker->randomNumber(5),
            'email' => $this->faker->email(),
            'phone' => $this->faker->e164PhoneNumber(),
            'status_id' => $statues[array_rand($statues)] ,
            'address' => $this->faker->address(),
//            'account_id' => Account::all()->random()->id

        ];
    }
}
