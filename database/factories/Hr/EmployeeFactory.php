<?php

namespace Database\Factories\Hr;

use App\Models\Hr\Department;
use App\Models\Hr\JopTitle;
use App\Models\System\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->e164PhoneNumber(),
            'address' => $this->faker->address(),
            'salary' => $this->faker->numberBetween(1500,8000),
            'joptitle_id'  => JopTitle::all()->random(1)->value('id'),
            'department_id' => Department::all()->random(1)->value('id'),
            'status_id'  => Status::where('type','jop')->first()->id,
            'active' => rand(0,1),
            'joined_at' => now()
        ];
    }
}
