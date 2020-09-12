<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

//buyers
Route::resource('buyers', 'Buyers\BuyerController', ['only' => ['index', 'show']]);
Route::resource('buyers.transactions', 'Buyers\BuyerTransactionController', ['only' => ['index']]);
Route::resource('buyers.products', 'Buyers\BuyerProductController', ['only' => ['index']]);
Route::resource('buyers.sellers', 'Buyers\BuyerSellerController', ['only' => ['index']]);
Route::resource('buyers.categories', 'Buyers\BuyerCategoryController', ['only' => ['index']]);
//sellers
Route::resource('sellers', 'Sellers\SellerController', ['only' => ['index', 'show']]);
Route::resource('sellers.transactions', 'Sellers\SellerTransactionController', ['only' => ['index']]);
Route::resource('sellers.categories', 'Sellers\SellerCategoryController', ['only' => ['index']]);
Route::resource('sellers.buyers', 'Sellers\SellerBuyerController', ['only' => ['index']]);
Route::resource('sellers.products', 'Sellers\SellerProductController', ['except' => ['create', 'show', 'edit']]);

//categories
Route::resource('categories', 'Categories\CategoryController', ['except' => ['create', 'edit']]);
Route::resource('categories.products', 'Categories\CategoryProductController', ['only' => ['index']]);
Route::resource('categories.sellers', 'Categories\CategorySellerController', ['only' => ['index']]);
Route::resource('categories.transactions', 'Categories\CategoryTransactionController', ['only' => ['index']]);
Route::resource('categories.buyers', 'Categories\CategoryBuyerController', ['only' => ['index']]);

//products
Route::resource('products', 'Products\ProductController', ['only' => ['index', 'show']]);
Route::resource('products.transactions', 'Products\ProductTransactionController', ['only' => ['index']]);
Route::resource('products.buyers', 'Products\ProductBuyerController', ['only' => ['index']]);
Route::resource('products.categories', 'Products\ProductCategoryController', ['only' => ['index', 'update', 'destroy']]);
Route::resource('products.buyers.transactions', 'Products\ProductBuyerTransactionController', ['only' => ['store']]);

//transactions
Route::resource('transactions', 'Transactions\TransactionController', ['only' => ['index', 'show']]);
Route::resource('transactions.categories', 'Transactions\TransactionCategoryController', ['only' => ['index']]);
Route::resource('transactions.sellers', 'Transactions\TransactionSellerController', ['only' => ['index']]);

//users
Route::resource('users', 'Users\UserController', ['except' => ['create', 'edit']]);
Route::name('verify')->get('users/verify/{token}', 'Users\UserController@verify');
Route::name('resend')->get('users/{user}/resend', 'Users\UserController@resend');

//now this will be using all the api middlewares
Route::post('oauth/token',
	'\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');