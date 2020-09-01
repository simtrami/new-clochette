<?php

/* @var $factory Factory */

use App\Bottle;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Bottle::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'quantity' => $faker->numberBetween(0, 100),
        'unit_price' => $faker->randomFloat(3, 0, 150.999),
        'volume' => $faker->randomFloat(3, 0.100, 2.999),
        'is_returnable' => $faker->boolean(),
        'abv' => $faker->randomFloat(1, 0, 99.99),
        'ibu' => $faker->randomFloat(1, 0, 999.9),
    ];
});
