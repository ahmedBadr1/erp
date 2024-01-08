<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\AccountType;
use App\Models\Accounting\Node;
use Illuminate\Database\Eloquent\Factories\Factory;

class NodeFactory extends Factory
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
            'parent_id' => rand(1,Node::count()),
            'account_type_id' => AccountType::all()->random()->id,
            'usable' => 0,
            'system' => 1,
        ];
    }
}
