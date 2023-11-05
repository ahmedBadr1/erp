<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->url(),
            'credit' => $this->faker->boolean,
            'parent_id' => rand(1,Category::count()),
            'usable' => 0,
            'system' => 1,
        ];
    }
}
