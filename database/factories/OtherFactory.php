<?php

/* @var $factory Factory */

use App\Other;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Other::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'quantity' => $faker->numberBetween(0, 100),
        'unit_price' => $faker->randomFloat(3, 0, 150.999),
        'description' => $faker->text(255),
    ];
});
