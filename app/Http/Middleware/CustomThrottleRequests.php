<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;

class CustomThrottleRequests extends ThrottleRequests {
	use ApiResponser;
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  int|string  $maxAttempts
	 * @param  float|int  $decayMinutes
	 * @param  string  $prefix
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
	 */
	public function handle($request, Closure $next, $maxAttempts = 30, $decayMinutes = 1, $prefix = '') {
		$key = $prefix . $this->resolveRequestSignature($request);

		$maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

		if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
			return $this->buildException($key, $maxAttempts);
		}

		$this->limiter->hit($key, $decayMinutes * 30);

		$response = $next($request);

		return $this->addHeaders(
			$response, $maxAttempts,
			$this->calculateRemainingAttempts($key, $maxAttempts)
		);
	}

	/**
	 * Create a 'too many attempts' exception.
	 *
	 * @param  string  $key
	 * @param  int  $maxAttempts
	 * @return \Illuminate\Http\Exceptions\ThrottleRequestsException
	 */
	protected function buildException($key, $maxAttempts) {
		$retryAfter = $this->getTimeUntilNextRetry($key);

		$headers = $this->getHeaders(
			$maxAttempts,
			$this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
			$retryAfter
		);

		return $this->errorResponse('Too Many Attempts.', 429);
	}
}
