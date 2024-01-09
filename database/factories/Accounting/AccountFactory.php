<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\AccountType;
use App\Models\Accounting\Node;
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
//  Category::withCount('ancestors' )->get()->where('ancestors_count','=',2)->random()->id,//
        return [
//            'code' =>$this->faker->numerify,
            'name' =>$this->faker->company,
            'credit' => rand(0, 1),
            'description' => $this->faker->text,
            'active' => rand(0, 1),
            'node_id' => Node::isLeaf()->get()->random()->id,//
            'currency_id' => Currency::all()->random()->id,
            'account_type_id' => AccountType::all()->random()->id,

//            'status_id' => $statues[array_rand($statues)],
//            'opening_balance' => $this->faker->numberBetween(100,10000),
//            'opening_date' => $this->faker->dateTimeThisYear(),
            'system' =>  1,
        ];
    }
}
