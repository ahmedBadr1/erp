<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\AccountType;
use App\Models\Accounting\Node;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->name();
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'credit' => $this->faker->boolean,
            'parent_id' => rand(1,Node::count()),
//            'account_type_id' => AccountType::all()->random()->id ,
            'usable' => 0,
            'system' => 1,
        ];
    }
}
