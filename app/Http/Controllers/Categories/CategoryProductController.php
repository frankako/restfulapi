<?php

namespace App\Http\Controllers\Categories;

use App\Category;
use App\Http\Controllers\ApiController;

class CategoryProductController extends ApiController {

	public function __construct() {

		$this->middleware('client.credentials')->only(['index']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Category $category) {
		$products = $category->products;
		return $this->showAll($products);
	}
}