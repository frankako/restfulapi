<?php

use App\Product;
use App\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Product::class, 1000)->create()->each(function($product){
          $categories = Category::all()->random(mt_rand(1, 5))->pluck('id');
          $product->categories()->attach($categories);
        });
    }
}
