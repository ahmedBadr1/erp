<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\AccountType;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\CostCenterNode;
use App\Models\Accounting\Node;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\CostCenter>
 */
class CostCenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->companySuffix(),
            'cost_center_node_id' => rand(1,CostCenterNode::count()),
            'system' => 1,
        ];
    }
}
