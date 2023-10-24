<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' =>$this->faker->numerify,
            'name' =>$this->faker->name,
            'type' => rand(0, 1) ? 'credit' : 'debit',
            'description' => $this->faker->text,
            'active' => rand(0, 1),
            'category_id' => Category::all()->random(1)->first()->id,
            'currency_id' => rand(1, 3),
            'status_id' => rand(1, 100),
        ];
    }
}
