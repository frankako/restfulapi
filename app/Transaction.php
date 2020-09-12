<?php

namespace App;

use App\Buyer;
use App\Product;
use App\Transformers\TransactionTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model {
	use SoftDeletes;

	public $transformer = TransactionTransformer::class;

	protected $fillable = ['quantity', 'product_id', 'buyer_id'];

	public function buyer() {
		return $this->belongsTo(Buyer::class);
	}

	public function product() {
		return $this->belongsTo(Product::class);
	}
}
