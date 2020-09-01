<?php

/* @var $factory Factory */

use App\Food;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Food::class, function (Faker $faker) {
    return [
        'is_bulk' => $faker->boolean
    ];
});
