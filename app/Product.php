<?php

namespace App;

use App\Category;
use App\Product;
use App\Seller;
use App\Transaction;
use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
	use SoftDeletes;

	public $transformer = ProductTransformer::class;

	const AVAILABLE = 'available';
	const UNAVAILABLE = 'unavailable';

	protected $date = ['deleted_at'];

	protected $fillable = ['name', 'description', 'image', 'quantity', 'status', 'seller_id'];

	protected $hidden = ['pivot'];

	public function isAvailable() {
		return $this->status == Product::AVAILABLE;
	}

	public function categories() {
		return $this->belongsToMany(Category::class);
	}

	public function seller() {
		return $this->belongsTo(Seller::class);
	}

	public function transactions() {
		return $this->hasMany(Transaction::class);
	}
}
