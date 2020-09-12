<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler {
	use ApiResponser;
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Report or log an exception.
	 *
	 * @param  \Throwable  $exception
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function report(Throwable $exception) {
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Throwable  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Throwable
	 */
	public function render($request, Throwable $exception) {
		if ($exception instanceof ValidationException) {
			return $this->convertValidationExceptionToResponse($exception, $request);
		}

		if ($exception instanceof ModelNotFoundException) {
			$modelName = strtolower(class_basename($exception->getModel()));
			return $this->errorResponse("There does not exists a {$modelName} with this id", 404);
		}

		if ($exception instanceof AuthenticationException) {
			return $this->unauthenticated($request, $exception);
		}

		if ($exception instanceof AuthorizationException) {
			return $this->errorResponse($exception->getMessage(), 403);
		}

		if ($exception instanceof NotFoundHttpException) {
			return $this->errorResponse("The specified url can not be found", 404);
		}

		if ($exception instanceof MethodNotAllowedHttpException) {
			return $this->errorResponse("The specified method for the request is invalid", 405);
		}

		//general rule to handle any other kind of http exception
		if ($exception instanceof HttpException) {
			$this->errorResponse($exception->getMessage(), $exception->getStatusCode());
		}

		//if a user is associated to a transaction or seller to product, we can not remove
		if ($exception instanceof QueryException) {
			//dd($exception) to get details of the exception
			$errorCode = $exception->errorInfo[1];

			if ($errorCode === 1451) {
				return $this->errorResponse("Can not remove resource. It is related with other resources", 409);
			}
		}

		if ($exception instanceof TokenMismatchException) {
			return redirect()->back()->withInput($request->input());
		}

		//handling unexpected exceptions. If in dev or production
		if (config('app.debug')) {
			return parent::render($request, $exception);
		}

		return $this->errorResponse("Unexpected Exception", 500);
	}

	/**
	 * Convert an authentication exception into a response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Auth\AuthenticationException  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function unauthenticated($request, AuthenticationException $exception) {

		if ($this->isFrontEnd($request)) {
			return redirect()->guest('login');
		}

		return $this->errorResponse("Unauthenticated", 401);
	}

	/**
	 * convertValidationExceptionToResponse from parent render method
	 * @param  ValidationException $e       exception
	 * @param  $request request
	 * @return
	 */
	protected function convertValidationExceptionToResponse(ValidationException $e, $request) {
		//we want a json response independently whether request expects json or not
		$errors = $e->validator->errors()->getMessages();

		if ($this->isFrontEnd($request)) {
			return $request->ajax() ? response()->json($errors, 422) : redirect()->back()
				->withInput($request->input())->withErrors($errors);
		}

		return $this->errorResponse($errors, 422);
	}

//if exception is from web route, we do not want a json response as we have above
	private function isFrontEnd($request) {
		return $request->acceptsHTML() && collect($request->route()->middleware())->contains('web');
		//we get the collection of the request route middleware and check if there is a web middleware within. Note we have a middleware for api and web routes in RouetServiceprovider
	}

}
