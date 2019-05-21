<?php

/* @var $factory Factory */

use App\Barrel;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Barrel::class, function (Faker $faker) {
    return [
        'volume' => $faker->randomFloat(2, 20, 50),
        'withdrawal_type' => $faker->randomElement(['Type A', 'Type S', 'Keykeg'])
    ];
});
