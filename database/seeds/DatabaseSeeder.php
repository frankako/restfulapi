<?php

use App\Category;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run() {
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		User::truncate();
		Category::truncate();
		Product::truncate();
		Transaction::truncate();
		DB::table('category_product')->truncate();

		User::flushEventListeteners();
		Category::flushEventListeteners();
		Product::flushEventListeteners();
		Transaction::flushEventListeteners();

		$this->call(UserSeeder::class);
		$this->call(CategorySeeder::class);
		$this->call(ProductSeeder::class);
		$this->call(TransactionSeeder::class);
	}
}
