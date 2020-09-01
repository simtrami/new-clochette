<?php

/* @var $factory Factory */

use App\PaymentMethod;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(PaymentMethod::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->monthName,
        'debit_customer' => $faker->boolean,
        'icon_name' => $faker->word,
    ];
});
