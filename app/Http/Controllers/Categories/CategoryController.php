<?php

namespace App\Http\Controllers\Categories;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;

class CategoryController extends ApiController {

	public function __construct() {
		$this->middleware('auth:api')->except(['index', 'show']);
		$this->middleware('client.credentials')->only(['index', 'show']);
		$this->middleware('transform.input:' . CategoryTransformer::class)->only(['store', 'update']);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$categories = Category::all();
		return $this->showAll($categories);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$rules = [
			'name' => 'required|min:4|unique:categories',
			'description' => 'required',
		];

		$this->validate($request, $rules);
		$category = Category::create($request->all());

		return $this->showOne($category, 201);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Category $category) {
		return $this->showOne($category);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Category $category) {
		$category->fill($request->only(['name', 'description']));

		if (!$category->isDirty()) {
			return $this->errorResponse('You have to make a change for update', 422);
		}

		$category->save();
		return $this->showOne($category);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$category->delete();
		return $this->showOne($category);
	}
}
