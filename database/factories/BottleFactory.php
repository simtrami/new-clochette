<?php

/* @var $factory Factory */

use App\Bottle;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Bottle::class, function (Faker $faker) {
    return [
        'volume' => $faker->randomFloat(3, 0.100, 2.999),
        'is_returnable' => $faker->boolean(),
        'abv' => $faker->randomFloat(2),
        'ibu' => $faker->randomFloat(1),
    ];
});
