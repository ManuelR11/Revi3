<?php

namespace App\Services;

use Exception;
use App\Models\CategoryComplement;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\CategoryComplementRequest;
use App\Libraries\QueryExceptionLibrary;

class CategoryComplementService
{
    protected array $filters = [
        'name',
        'description',
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return CategoryComplement::where(function ($query) use ($requests) {
                foreach ($requests as $key => $value) {
                    if (in_array($key, $this->filters)) {
                        $query->where($key, 'like', '%' . $value . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(CategoryComplementRequest $request)
    {
        try {
            return CategoryComplement::create($request->validated());
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(CategoryComplement $categoryComplement)
    {
        try {
            return $categoryComplement;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(CategoryComplementRequest $request, CategoryComplement $categoryComplement)
    {
        try {
            return tap($categoryComplement)->update($request->validated());
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(CategoryComplement $categoryComplement): void
    {
        try {
            $categoryComplement->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}