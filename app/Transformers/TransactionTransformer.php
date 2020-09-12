<?php

namespace App\Transformers;

use App\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract {

	/**
	 * A Fractal transformer.
	 *
	 * @return array
	 */
	public function transform(Transaction $transaction) {
		return [
			'identifier' => (int) $transaction->id,
			'product' => (int) $transaction->product_id,
			'buyer' => (int) $transaction->buyer_id,
			'number' => (int) $transaction->quantity,
			'creationDate' => (string) $transaction->created_at,
			'lastChange' => (string) $transaction->updated_at,
			'deletedDate' => isset($transaction->deleted_at) ? (string) $transaction->deleted_at : null,

			'links' => [
				[
					'rel' => 'self',
					'href' => route('transactions.show', $transaction->id),
				],
				[
					'rel' => 'transaction.categories',
					'href' => route('transactions.categories.index', $transaction->id),
				],
				[
					'rel' => 'buyer',
					'href' => route('buyers.show', $transaction->buyer_id),
				],
				[
					'rel' => 'product',
					'href' => route('products.show', $transaction->product_id),
				],
				[
					'rel' => 'transaction.seller',
					'href' => route('transactions.sellers.index', $transaction->id),
				],
			],
		];
	}

	public static function mapOriginalAttribute($index) {
		$attributes = [
			'identifier' => 'id',
			'product' => 'product_id',
			'buyer' => 'buyer_id',
			'number' => 'quantity',
			'creationDate' => 'created_at',
			'lastChange' => 'updated_at',
			'deletedDate' => 'deleted_at',
		];
		return isset($attributes[$index]) ? $attributes[$index] : null;
	}

	public static function mapValidationAttributes($index) {
		$attributes = [
			'id' => 'identifier',
			'product_id' => 'product',
			'buyer_id' => 'buyer',
			'quantity' => 'number',
			'created_at' => 'creationDate',
			'updated_at' => 'lastChange',
			'deleted_at' => 'deletedDate',
		];

		return isset($attributes[$index]) ? $attributes[$index] : null;
	}
}
