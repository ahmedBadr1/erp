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
            'type' => Category::$types[rand(0,1)],
            'parent_id' => rand(1,Category::count())
        ];
    }
}
