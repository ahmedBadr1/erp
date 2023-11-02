<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Category;
use App\Models\System\Currency;
use App\Models\System\Status;
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
//        $statues = Status::where('type','account')->pluck('id')->toArray();
        return [
            'code' =>$this->faker->numerify,
            'name' =>$this->faker->name,
            'credit' => rand(0, 1),
            'description' => $this->faker->text,
            'active' => rand(0, 1),
            'category_id' => Category::all()->random()->id,
            'currency_id' => Currency::all()->random()->id,
//            'status_id' => $statues[array_rand($statues)],
//            'opening_balance' => $this->faker->numberBetween(100,10000),
//            'opening_balance_date' => $this->faker->dateTimeThisYear(),
            'system' => rand(0, 1),
        ];
    }
}
