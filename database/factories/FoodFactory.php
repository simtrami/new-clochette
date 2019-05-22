<?php

/* @var $factory Factory */

use App\Food;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Food::class, function (Faker $faker) {
    $ret = [
        'is_bulk' => $faker->boolean
    ];

    if ($ret['is_bulk'] === true) {
        $ret['units_left'] = $faker->numberBetween(0, 100);
    }

    return $ret;
});
