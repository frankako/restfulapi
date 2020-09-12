<?php

namespace App\Providers;

use App\Mail\UserCreated;
use App\Mail\UserEmailUpdated;
use App\Product;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {

		User::created(function ($user) {
			retry(5, function () use ($user) {
				Mail::to($user)->send(new UserCreated($user));
			}, 5000);
		});

		User::updated(function ($user) {
			if ($user->isDirty('email')) {
				retry(5, function () use ($user) {
					Mail::to($user)->send(new UserEmailUpdated($user));
				}, 5000);
			}
		});

		Product::updated(function ($product) {
			if ($product->quantity == 0 && $product->isAvailable()) {
				$product->status = Product::UNAVAILABLE;
				$product->save();
			}
		});
	}
}
