<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Complement;
use App\Services\ComplementService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ComplementRequest;
use App\Http\Resources\ComplementResource;

class ComplementController extends AdminController
{
    private ComplementService $complementService;

    public function __construct(ComplementService $complementService)
    {
        parent::__construct();
        $this->complementService = $complementService;
        $this->middleware(['permission:settings'])->only('store', 'update', 'destroy', 'show');
    }

    public function index(PaginateRequest $request)
    {
        try {
            return ComplementResource::collection($this->complementService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ComplementRequest $request)
    {
        try {
            return new ComplementResource($this->complementService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Complement $complement)
    {
        try {
            return new ComplementResource($this->complementService->show($complement));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ComplementRequest $request, Complement $complement)
    {
        try {
            return new ComplementResource($this->complementService->update($request, $complement));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Complement $complement)
    {
        try {
            $this->complementService->destroy($complement);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}