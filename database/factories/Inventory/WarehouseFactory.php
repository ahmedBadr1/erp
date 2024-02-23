<?php

namespace Database\Factories\Inventory;

use App\Models\Accounting\Account;
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
//            'type' => $this->faker->name(),
            'description' => $this->faker->name(),
            'space' => $this->faker->randomNumber(),
            'height' => $this->faker->randomNumber(),
            'is_rma' => $this->faker->boolean(),
            'is_rented' => $this->faker->boolean(),
            'has_security' => $this->faker->boolean(),
//            'address' => $this->faker->address(),
//            'manager_id' => Employee::all()->random()->value('id') ?? null,
            'account_id' => Account::whereHas('type',fn($q)=>$q->where('code','I'))->value('id'),//->random()->id

        ];
    }
}
