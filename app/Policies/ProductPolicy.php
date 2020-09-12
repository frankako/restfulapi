<?php

namespace App\Policies;

use App\Product;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy {
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the model.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Product  $product
	 * @return mixed
	 */
	public function addCategory(User $user, Product $product) {
		$user->id === $product->seller_id;
	}

	/**
	 * Determine whether the user can delete the model.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Product  $product
	 * @return mixed
	 */
	public function deleteCategory(User $user, Product $product) {
		$user->id === $product->seller_id;
	}
}
