<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\DevelopmentStoreRequest;
use App\Http\Requests\DevelopmentUpdateRequest;
use App\Http\Resources\DevelopmentResource;
use App\Models\Development;
use App\Services\DevelopmentService;
use Illuminate\Http\Request;

class DevelopmentController extends Controller
{
    public function __construct(
        private DevelopmentService $developmentService
    ){}
    
    public function index()
    {
        //
    }

    public function store(DevelopmentStoreRequest $request)
    {
       $development = $this->developmentService->createDevelopment($request->validated(), $request->file('image') ?? null);
        return ResponseHelper::success('Development created successfully', [
            new DevelopmentResource($development)
        ], 201);
    }

    public function show(string $id)
    {
        //
    }

    public function update(DevelopmentUpdateRequest $request, Development $development)
    {
        $this->developmentService->updateDevelopment($request->validated(), $request->file('image') ?? null, $development);
        return ResponseHelper::success('Development updated successfully', [
            new DevelopmentResource($development->fresh())
        ], 200);
    }

    public function destroy(Development $development)
    {
        $this->developmentService->deleteDevelopment($development);
        return ResponseHelper::success('Development deleted successfully', null, 200);
    }
}
