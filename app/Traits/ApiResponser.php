<?php
namespace App\Traits;
//use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponser {

	private function successResponse(array $data, int $code) {
		return response()->json($data, $code);
	}

	protected function errorResponse($data, int $code) {
		return response()->json(['error' => $data, 'code' => $code], $code);
	}

	public function showAll(Collection $collection, int $code = 200) {

		if ($collection->isEmpty()) {
			return $this->successResponse(['data' => $collection], $code);
		}

		$transformer = $collection->first()->transformer;
		$collection = $this->filterData($collection, $transformer);
		$collection = $this->sortData($collection, $transformer);
		$collection = $this->paginate($collection);
		$collection = $this->transformData($collection, $transformer);
		$collection = $this->cacheResponse($collection);
		//at this point collection is an array not intance of collection, so sortBy comes before

		return $this->successResponse($collection, $code);
	}

	public function showOne(Model $instance, int $code = 200) {
		$transformer = $instance->transformer;
		$instance = $this->transformData($instance, $transformer);
		return $this->successResponse($instance, $code);
	}

	public function showMessage($message, int $code = 200) {
		return $this->successResponse(['data' => $message], $code);
	}

	private function filterData(Collection $collection, $transformer) {
		foreach (request()->query() as $queryParam => $value) {
			$queryParam = $transformer::mapOriginalAttribute($queryParam);
			if (isset($queryParam, $value)) {
				$collection = $collection->where($queryParam, $value);
			}
		}

		return $collection;
	}

	private function sortData(Collection $collection, $transformer) {
		if (request()->has('sort_by')) {
			$attribute = $transformer::mapOriginalAttribute(request()->sort_by);

			$collection = $collection->sortBy->{$attribute};
		}

		return $collection;
	}

	private function paginate(Collection $collection) {

		$rules = [
			'per_page' => 'integer|max:100',
		];

		Validator::validate(request()->all(), $rules);

		$page = LengthAwarePaginator::resolveCurrentPage();

		$per_page = 15;

		if (request()->has('per_page') && request()->per_page > 0) {
			$per_page = (int) request()->per_page;
		}

		$results = $collection->slice(($page - 1) * $per_page, $per_page)->values();
		$pagination = new LengthAwarePaginator($results, $collection->count(), $per_page, $page, [
			'path' => LengthAwarePaginator::resolveCurrentPath(),
		]);

		$pagination->appends(request()->all());

		return $pagination;
	}

	private function transformData($data, $transformer) {
		$transformation = fractal($data, new $transformer);

		return $transformation->toArray();
	}

	private function cacheResponse($data) {
		$url = request()->url();
		$queryParams = request()->query();

		ksort($queryParams);
		$buildQueryString = http_build_query($queryParams);

		$fullUrl = "{$url}?{$buildQueryString}";

		return Cache::remember($fullUrl, 30, function () use ($data) {
			return $data;
		});
	}
}

?>