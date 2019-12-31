<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'email'    => $faker->unique()->safeEmail,
        'password' => $faker->unique()->password(8, 10)
    ];
});
