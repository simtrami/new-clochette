<?php

namespace Database\Factories;

use App\Barrel;
use App\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarrelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Barrel::class;

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
            'volume' => $this->faker->randomFloat(2, 20, 50),
            'coupler' => $this->faker->randomElement(['Type A', 'Type S', 'Keykeg']),
            'abv' => $this->faker->randomFloat(1, 0, 99.99),
            'ibu' => $this->faker->randomFloat(1, 0, 999.9),
        ];
    }
}
