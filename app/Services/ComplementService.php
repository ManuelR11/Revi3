<?php

namespace App\Services;

use Exception;
use App\Models\Complement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ComplementRequest;
use App\Libraries\QueryExceptionLibrary;

class ComplementService
{
    protected array $filters = [
        'name',
        'description',
        'status',
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

            return Complement::with('categories')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $value) {
                    if (! in_array($key, $this->filters, true)) {
                        continue;
                    }

                    if ($value === '' || $value === null) {
                        continue;
                    }

                    if ($key === 'status') {
                        $query->where($key, $value);
                    } else {
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
    public function store(ComplementRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $categories = $data['categories'] ?? [];
                unset($data['categories']);

                $complement = Complement::create($data);
                $complement->categories()->sync($categories);

                return $complement->load('categories');
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Complement $complement)
    {
        try {
            return $complement->load('categories');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ComplementRequest $request, Complement $complement)
    {
        try {
            return DB::transaction(function () use ($request, $complement) {
                $data = $request->validated();
                $categories = $data['categories'] ?? null;
                unset($data['categories']);

                $complement->update($data);

                if (! is_null($categories)) {
                    $complement->categories()->sync($categories);
                }

                return $complement->load('categories');
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Complement $complement): void
    {
        try {
            $complement->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}