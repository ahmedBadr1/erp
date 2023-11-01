<?php

namespace Database\Factories\Crm;

use App\Models\Crm\Action;
use App\Models\Employee\Employee;
use App\Models\Crm\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Action>
 */
class ActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => Action::$types[rand(0,4)],
            'description' => $this->faker->realText(),
            'due_at' => $this->faker->dateTime,
            'done_at'=> $this->faker->dateTime,
//            'employee_id' => Employee::all()->random()->value('id'),
            'user_id' => User::all()->random()->value('id'),
            'client_id'=> Client::all()->random()->value('id'),
        ];
    }
}
