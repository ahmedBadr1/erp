<?php

namespace Database\Factories\Purchases;

use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->firstName(),
            'business_name' => $this->faker->company(),
            'code' => $this->faker->postcode(),
            'telephone' => $this->faker->phoneNumber(),
            'phone' => $this->faker->e164PhoneNumber(),
            'email'  => fake()->unique()->safeEmail(),
            'website' => $this->faker->domainName(),
            'credit_limit' => $this->faker->numerify(),
            'tax_number' => $this->faker->randomNumber(),
            'registration_number' => $this->faker->randomNumber(),
            'payment_method' => $this->faker->creditCardNumber(),
            'responsible_id'  => Employee::all()->random(1)->value('id'),
            'warranty' => $this->faker->month(),
//            'business_name', 'name', 'code', 'responsible_id', 'credit_limit', 'tax_number',
//        'registration_number', 'payment_method', 'phone', 'telephone', 'email', 'active'
        ];
    }
}
