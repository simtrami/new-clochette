<?php

/* @var $factory Factory */

use App\Kit;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Kit::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'quantity' => $faker->numberBetween(0, 100),
    ];
});
