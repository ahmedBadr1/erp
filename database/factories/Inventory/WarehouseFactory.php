<?php

namespace Database\Factories\Inventory;

use App\Models\Employee\Employee;
use App\Models\Inventory\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Warehouse::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'location' => $this->faker->address(),
//            'manager_id' => Employee::all()->random()->value('id') ?? null,
        ];
    }
}
