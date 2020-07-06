<?php

/* @var $factory Factory */

use App\Customer;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'nickname' => $faker->unique()->userName,
        'balance' => $faker->randomFloat(2, -200, 200),
        'is_staff' => $faker->randomElement([false, true]),
    ];
});
