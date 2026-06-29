<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocialAssistanceStoreRequest;
use App\Http\Requests\SocialAssistanceUpdateRequest;
use App\Http\Resources\SocialAssistanceResource;
use App\Models\SocialAssistance;
use App\Services\SocialAssistanceService;

class SocialAssistanceController extends Controller
{
    public function __construct(private SocialAssistanceService $socialAssistanceService) {}

    public function index()
    {
        $socialAssistances = SocialAssistance::with('file')->paginate(20);

        return ResponseHelper::success(
            'Social assistances retrieved successfully',
            SocialAssistanceResource::collection($socialAssistances),
            200
        );
    }

    public function store(SocialAssistanceStoreRequest $request)
    {
        $socialAssistance = $this->socialAssistanceService->createSocialAssistance($request->validated(), $request->file('image') ?? null);

        return ResponseHelper::success('Social assistance created successfully', [
            new SocialAssistanceResource($socialAssistance),
        ], 201);
    }

    public function show(SocialAssistance $socialAssistance)
    {
        $socialAssistance->load('file');

        return ResponseHelper::success(
            'Social assistance retrieved successfully',
            new SocialAssistanceResource($socialAssistance),
            200
        );
    }

    public function update(SocialAssistanceUpdateRequest $request, SocialAssistance $socialAssistance)
    {
        $socialAssistance = $this->socialAssistanceService->updateSocialAssistance($request->validated(), $request->file('image') ?? null, $socialAssistance);

        return ResponseHelper::success('Social assistance updated successfully', [
            new SocialAssistanceResource($socialAssistance),
        ], 200);
    }

    public function destroy(SocialAssistance $socialAssistance)
    {
        $this->socialAssistanceService->deleteSocialAssistance($socialAssistance);

        return ResponseHelper::success('Social assistance deleted successfully', null, 200);
    }
}
