<?php

/* @var $factory Factory */

use App\PaymentMethod;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(PaymentMethod::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->monthName,
        'needs_cash_drawer' => $faker->boolean,
    ];
});
