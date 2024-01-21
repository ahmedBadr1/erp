<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\CostCenterNode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\CostCenterNode>
 */
class CostCenterNodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        return [
            'name' => $name,
            'slug' =>Str::slug($name),
//            'parent_id' => random_int(1,CostCenterNode::count()),
        ];
    }
}
