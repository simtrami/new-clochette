<?php

/* @var $factory Factory */

use App\Article;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'unit_price' => $faker->randomFloat(3, 0, 150.999)
    ];
});
