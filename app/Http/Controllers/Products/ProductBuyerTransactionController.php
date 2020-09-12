<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Transaction;
use App\Transformers\ProductTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController {

	public function __construct() {
		parent::__construct();
		$this->middleware('transform.input:' . ProductTransformer::class)->only(['store']);
		$this->middleware('scope:purchase-product')->only(['store']);
	}

	/**
	 * Store a newly created resource in storage.
	 *App\User buyer may be first time buyer
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, Product $product, User $buyer) {

		$rules = [
			"quantity" => 'required|integer|min:1',
		];

		$this->validate($request, $rules);

		if ($buyer->id == $product->seller_id) {
			return $this->errResponse("The buyer has to be different from seller", 409);
		}

		if (!$buyer->isVerified()) {
			return $this->errorResponse("The buyer has to be verified", 409);
		}

		if (!$product->seller->isVerified()) {
			return $this->errorResponse("The seller has to be verified", 409);
		}

		if (!$product->isAvailable()) {
			return $this->errorResponse("The product is not available", 409);
		}

		if ($product->quantity < $request->quantity) {
			return $this->errorResponse("There is not enough products for this transaction", 409);
		}

		return DB::transaction(function () use ($request, $product, $buyer) {
			$product->quantity -= $request->quantity;
			$product->save();

			$transaction = Transaction::create([
				'product_id' => $product->id,
				'buyer_id' => $buyer->id,
				'quantity' => $request->quantity,
			]);

			return $this->showOne($transaction);
		});
	}
}
