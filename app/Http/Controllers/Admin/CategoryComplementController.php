<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\CategoryComplement;
use App\Services\CategoryComplementService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\CategoryComplementRequest;
use App\Http\Resources\CategoryComplementResource;

class CategoryComplementController extends AdminController
{
    private CategoryComplementService $categoryComplementService;

    public function __construct(CategoryComplementService $categoryComplementService)
    {
        parent::__construct();
        $this->categoryComplementService = $categoryComplementService;
        $this->middleware(['permission:settings'])->only('store', 'update', 'destroy', 'show');
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return CategoryComplementResource::collection($this->categoryComplementService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CategoryComplementRequest $request): \Illuminate\Http\Response | CategoryComplementResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CategoryComplementResource($this->categoryComplementService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(CategoryComplement $categoryComplement): \Illuminate\Http\Response | CategoryComplementResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CategoryComplementResource($this->categoryComplementService->show($categoryComplement));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(CategoryComplementRequest $request, CategoryComplement $categoryComplement): \Illuminate\Http\Response | CategoryComplementResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CategoryComplementResource($this->categoryComplementService->update($request, $categoryComplement));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(CategoryComplement $categoryComplement): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->categoryComplementService->destroy($categoryComplement);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}