<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Seller;
use App\User;
use App\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
	$seller = Seller::has('products')->get()->random();
	$buyer = User::all()->except($seller->id)->random();
	//we get all users because a user may just be purchasing
    return [
         'quantity' => $faker->numberBetween(1, 3),
         'buyer_id' => $buyer->id,
         'product_id' => $seller->products->random()->id,
    ];
});
