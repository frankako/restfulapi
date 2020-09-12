<?php

namespace App\Policies;

use App\Seller;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellerPolicy {
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the model.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Seller  $seller
	 * @return mixed
	 */
	public function view(User $user, Seller $seller) {
		$user->id === $seller->id;
	}

	/**
	 * Determine whether the user can update the model.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Seller  $seller
	 * @return mixed
	 */
	public function sale(User $user, User $seller) {
		$user->id === $seller->id;
	}

	/**
	 * Determine whether the user can edit the model.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Seller  $seller
	 * @return mixed
	 */
	public function editProduct(User $user, Seller $seller) {
		$user->id === $seller->id;
	}

	/**
	 * Determine whether the user can delete the model.
	 *
	 * @param  \App\User  $user
	 * @param  \App\Seller  $seller
	 * @return mixed
	 */
	public function deleteProduct(User $user, Seller $seller) {
		$user->id === $seller->id;
	}

}
