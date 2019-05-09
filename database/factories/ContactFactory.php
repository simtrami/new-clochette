<?php

/* @var $factory Factory */

use App\Contact;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'phone' => $faker->phoneNumber,
        'email' => $faker->safeEmail,
        'role' => $faker->jobTitle,
        'notes' => $faker->paragraph
    ];
});
