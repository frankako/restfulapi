<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\Transformers\SellerTransformer;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController {

	public function __construct() {
		parent::__construct();

		$this->middleware('transform.input:' . SellerTransformer::class)->only(['store', 'update']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Seller $seller) {
		$products = $seller->products;
		return $this->showAll($products);
	}

	/**
	 * Store a newly created resource in storage.
	 *App\User so that users without product can create one
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, User $seller) {
		$rules = [
			'name' => 'required',
			'description' => 'required',
			'quantity' => 'required|integer|min:1',
			'image' => 'required|image',
		];

		$this->validate($request, $rules);
		$data = $request->all();
		$data['status'] = Product::UNAVAILABLE;
		$data['image'] = $request->image->store('');
		$data['seller_id'] = $seller->id;

		$product = Product::create($data);
		return $this->showOne($product, 201);

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Seller  $seller
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Seller $seller, Product $product) {

		$this->IsSellerProduct($seller, $product);

		$rules = [
			'quantity' => 'integer|min:1',
			'status' => 'in:' . Product::AVAILABLE . "," . Product::UNAVAILABLE,
			'image' => 'image',
		];

		$this->validate($request, $rules);

		$product->fill($request->only([
			'name', 'description', 'quantity',
		]));

		if ($request->has('status')) {
			$product->status = $request->status;

			if ($product->isAvailable() && $product->categories()->count() == 0) {
				return $this->errorResponse("An active product should have atleast 1 category", 409);
			}
		}

		if ($request->hasFile('image')) {
			Storage::delete($poduct->image);
			$product->image = $request->image->store('');
		}

		if ($product->isClean()) {
			return $this->errorResponse("You have to make a change to update");
		}

		$product->save();
		return $this->showOne($product);
	}

	private function IsSellerProduct($seller, $product) {
		if ($seller->id != $product->seller_id) {
			throw new HttpException(422, "Only the owner of a product can update the product");
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Seller  $seller
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Seller $seller, Product $product) {
		$this->IsSellerProduct($seller, $product);
		//To permanently rmemove the image Storage:delete($product->image);
		$product->delete();
		return $this->showOne($product);

	}
}
