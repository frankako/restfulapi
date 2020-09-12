<?php

namespace App\Http\Controllers\Buyers;

use App\Buyer;
use App\Http\Controllers\ApiController;

class BuyerProductController extends ApiController {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Buyer $buyer) {
		$products = $buyer->transactions()->with('product')->get()->pluck('product');
		return $this->showAll($products);
	}

}
