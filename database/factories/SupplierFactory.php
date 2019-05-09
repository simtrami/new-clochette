<?php

/* @var $factory Factory */

use App\Supplier;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Supplier::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'description' => $faker->paragraph,
        'address' => $faker->address,
        'phone' => $faker->phoneNumber,
        'email' => $faker->companyEmail,
        'supplier_since' => $faker->date()
    ];
});
