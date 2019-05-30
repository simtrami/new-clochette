<?php

/* @var $factory Factory */

use App\TransactionDetail;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(TransactionDetail::class, function (Faker $faker) {
    return [
        'quantity' => random_int(1, 10),
    ];
});
