<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class TransformInput {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $transformer) {
		$transformedInput = [];

		foreach ($request->request->all() as $input => $value) {
			//to get just the content of the request
			$transformedInput[$transformer::mapOriginalAttribute($input)] = $value;
		}

		$request->replace($transformedInput);

		$response = $next($request);

		if (isset($response->exception) && $response->exception instanceof ValidationException) {

			$data = $response->getData();
			$transformedErrors = [];

			foreach ($data->error as $field => $errorMessage) {
				$transformedField = $transformer::mapValidationAttributes($field);
				$transformedErrors[$transformedField] = str_replace($field, $transformedField, $errorMessage); //value = $errorMessage
			}

			$data->error = $transformedErrors;
			$response->setData($data);
		}

		return $response;
	}
}
