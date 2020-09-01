<?php

/* @var $factory Factory */

use App\Barrel;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Barrel::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'quantity' => $faker->numberBetween(0, 100),
        'unit_price' => $faker->randomFloat(3, 0, 150.999),
        'volume' => $faker->randomFloat(2, 20, 50),
        'coupler' => $faker->randomElement(['Type A', 'Type S', 'Keykeg']),
        'abv' => $faker->randomFloat(1, 0, 99.99),
        'ibu' => $faker->randomFloat(1, 0, 999.9),
    ];
});
