<?php

namespace Database\Factories;

use App\Food;
use App\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Food::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name' => $this->faker->colorName,
            'quantity' => $this->faker->numberBetween(0, 100),
            'unit_price' => $this->faker->randomFloat(3, 0, 150.999),
            'is_bulk' => $this->faker->boolean,
        ];
    }
}
