<?php

namespace Database\Factories;

use App\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nickname' => $this->faker->unique()->userName,
            'balance' => $this->faker->randomFloat(2, -200, 200),
            'is_staff' => $this->faker->randomElement([false, true]),
        ];
    }
}
