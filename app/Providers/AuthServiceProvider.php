<?php

namespace App\Providers;
use App\Buyer;
use App\Policies\BuyerPolicy;
use App\Policies\SellerPolicy;
use App\Policies\UserPolicy;
use App\Seller;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider {
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		// 'App\Model' => 'App\Policies\ModelPolicy',
		Buyer::class => BuyerPolicy::class,
		Seller::class => SellerPolicy::class,
		User::class => UserPolicy::class,
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->registerPolicies();

		Passport::routes();
		Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
		Passport::refreshTokensExpireIn(carbon::now()->addDays(30));
		Passport::personalAccessTokensExpireIn(Carbon::now()->addMonths(2));

		//Register implicit grant type
		Passport::enableImplicitGrant();

		//Scopes for api
		Passport::tokensCan(['purchase-product' => 'Create transactions for products',
			'manage-product' => 'Create, read, delete, update products',
			'manage-account' => 'Read account data if verified or is admin modify data but can not remove account',
			'read-general' => 'Read general information like purcahsing categories, selling proucts, your transactions etc',
		]);
	}
}
