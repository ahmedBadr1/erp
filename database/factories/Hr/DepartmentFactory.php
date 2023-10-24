<?php

namespace Database\Factories\Hr;

use App\Models\Employee\Employee;
use App\Models\System\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hr\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle,
            'description' => $this->faker->title,
            'active'=> true,
            'manager_id' => Employee::all()->random(1)->first()->id
        ];
    }
}
