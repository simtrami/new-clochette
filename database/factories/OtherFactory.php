<?php

namespace Database\Factories;

use App\Models\Other;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Other::class;

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
            'description' => $this->faker->text(255),
        ];
    }
}
