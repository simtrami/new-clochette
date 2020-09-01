<?php

/* @var $factory Factory */

use App\Food;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Food::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'quantity' => $faker->numberBetween(0, 100),
        'unit_price' => $faker->randomFloat(3, 0, 150.999),
        'is_bulk' => $faker->boolean,
    ];
});
