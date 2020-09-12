<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController {

	public function __construct() {
		$this->middleware('auth:api')->except(['store', 'resend', 'verify']);
		$this->middleware('client.credentials')->only(['store', 'resend']);
		$this->middleware('transform.input:' . UserTransformer::class)->only(['store', 'update']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$users = User::all();
		return $this->showAll($users);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$rules = [
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required|min:6|confirmed',
		];

		$this->validate($request, $rules);
		$data = $request->all();
		$data['verified'] = User::UNVERIFIED_USER;
		$data['verification_token'] = User::generateVerificationCode();
		$data['password'] = bcrypt($request->password);
		$data['admin'] = User::REGULAR_USER;

		$user = User::create($data);

		return $this->showOne($user);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(User $user) {
		return $this->showOne($user);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, User $user) {
		//make sure user exists

		$rules = [
			'email' => 'email|unique:users,email,' . $user->id,
			'password' => 'min:6|confirmed',
			'admin' => 'in:' . User::REGULAR_USER . ',' . User::ADMIN_USER,
		];

		$this->validate($request, $rules);

		if ($request->has('name')) {
			$user->name = $request->name;
		}

		if ($request->has('email') && $user->email != $request->email) {
			$user->verified = User::UNVERIFIED_USER;
			$user->verification_token = User::generateVerificationCode();
			$user->email = $request->email;
		}

		if ($request->has('admin')) {
			if (!$user->isVerified()) {
				return $this->errorResponse('This action can only be performed by a verified user', 409);
			}

			$user->admin = $request->admin;
		}

		if ($request->has('password')) {
			$user->password = bcrypt($request->password);
		}

		if (!$user->isDirty()) {
			return $this->errorResponse('You have to make a change to update account', 422);
		}

		$user->save();
		return $this->showOne($user, 201);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(User $user) {
		$user->delete();
		return $this->showOne($user);
	}

	public function verify($token) {
		$user = User::where('verification_token', $token)->firstOrFail();
		if ($user) {
			$user->verification_token = null;
			$user->verified = User::VERIFIED_USER;
			$user->save();
		} else {
			return $this->errorResponse("User not found. Please register!", 422);
		}

		return $this->showMessage("Account verified successfully");
	}

	public function resend(User $user) {

		if ($user->isVerified()) {
			return $this->errorResponse("User account is already verifed", 409);
		}

		Mail::to($user)->send(new UserCreated($user));

		return $this->showMessage("A new email verification email has been sent");
	}
}
