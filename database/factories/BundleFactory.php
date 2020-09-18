<?php

namespace Database\Factories;

use App\Bundle;
use App\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class BundleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bundle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->colorName,
            'quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}
