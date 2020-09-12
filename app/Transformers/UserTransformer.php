<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract {

	/**
	 * A Fractal transformer.
	 *
	 * @return array
	 */
	public function transform(User $user) {
		return [
			'identifier' => (int) $user->id,
			'name' => (string) $user->name,
			'email' => (string) $user->email,
			'isVerified' => (int) $user->verified,
			'isAdmin' => ($user->admin === 'true'), //this will return true if condition is true
			'creationDate' => (string) $user->created_at,
			'lastChange' => (string) $user->updated_at,
			'deletedDate' => isset($user->deleted_at) ? (string) $user->deleted_at : null,

		];
	}

	public static function mapOriginalAttribute($index) {
		$attributes = [
			'identifier' => 'id',
			'name' => 'name',
			'email' => 'email',
			'isAdmin' => 'admin',
			'password' => 'password',
			'isVerified' => 'verified',
			'creationDate' => 'created_at',
			'lastChange' => 'updated_at',
			'deletedDate' => 'deleted_at',
		];
		return isset($attributes[$index]) ? $attributes[$index] : null;
	}

	public static function mapValidationAttributes($index) {
		$attributes = [
			'id' => 'identifier',
			'name' => 'name',
			'email' => 'email',
			'admin' => 'isAdmin',
			'password' => 'password',
			'verified' => 'isVerified',
			'created_at' => 'creationDate',
			'updated_at' => 'lastChange',
			'deleted_at' => 'deletedDate',
		];

		return isset($attributes[$index]) ? $attributes[$index] : null;
	}
}
