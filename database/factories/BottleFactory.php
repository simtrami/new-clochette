<?php

namespace Database\Factories;

use App\Models\Bottle;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class BottleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bottle::class;

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
            'volume' => $this->faker->randomFloat(3, 0.100, 2.999),
            'is_returnable' => $this->faker->boolean(),
            'abv' => $this->faker->randomFloat(1, 0, 99.99),
            'ibu' => $this->faker->randomFloat(1, 0, 999.9),
        ];
    }
}
