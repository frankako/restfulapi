<?php

namespace App;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable {
	use Notifiable, SoftDeletes, HasApiTokens;

	public $transformer = UserTransformer::class;

	const VERIFIED_USER = '1';
	const UNVERIFIED_USER = '0';
	const ADMIN_USER = 'true';
	const REGULAR_USER = 'false';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password', 'admin', 'verification_token', 'verified',
	];

	protected $table = 'users';

	protected $date = ['deleted_at'];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token', //'verification_token',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	/** Mutators and Accesors */
	public function setNameAttribute($name) {
		$this->attributes['name'] = strtolower($name);
	}

	public function getNameAttribute($name) {
		return ucwords($name);
	}

	/** Mutators and Accesors */
	public function setEmailAttribute($email) {
		$this->attributes['email'] = strtolower($email);
	}

	public function isAdmin() {
		return $this->admin == User::ADMIN_USER;
	}

	public function isVerified() {
		return $this->verified == User::VERIFIED_USER;
	}

	public static function generateVerificationCode() {
		return Str::random(40);
	}
}
