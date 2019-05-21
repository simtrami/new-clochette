<?php

/* @var $factory Factory */

use App\Price;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Price::class, function (Faker $faker) {
    return [
        'value' => $faker->randomFloat(2, 0, 15.99)
    ];
});
