<?php

/* @var $factory Factory */

use App\Other;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Other::class, function (Faker $faker) {
    return [
        'description' => $faker->text(255)
    ];
});
