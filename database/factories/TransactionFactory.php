<?php

/* @var $factory Factory */

use App\Transaction;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'value' => $faker->randomFloat(2, -10, 100),
    ];
});
