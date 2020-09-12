<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {

    return [
    	 'name' => $faker->word,
         'description' => $faker->paragraph(1),
         'quantity' => $qty =  $faker->numberBetween(0, 20),
         'image' => $faker->randomElement(['laravel.jpg', 'php.jpg', 'wordpress.jpg']),
         'status' => $qty = 0 ? Product::UNAVAILABLE : Product::AVAILABLE,
         'seller_id' => User::all()->random()->id, //User::inRandomOrder()->first()->id
    ];
});
