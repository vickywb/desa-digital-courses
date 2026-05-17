<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocialAssistanceStoreRequest;
use App\Http\Requests\SocialAssistanceUpdateRequest;
use App\Http\Resources\SocialAssistanceResource;
use App\Models\SocialAssistance;
use App\Services\SocialAssistanceService;
use Illuminate\Http\Request;

class SocialAssistanceController extends Controller
{
    public function __construct(private SocialAssistanceService $socialAssistanceService){}

    public function index()
    {
        //
    }

    public function store(SocialAssistanceStoreRequest $request)
    {
        $socialAssistance = $this->socialAssistanceService->createSocialAssistance($request->validated(), $request->file('image') ?? null);

        return ResponseHelper::success('Social assistance created successfully', [
            new SocialAssistanceResource($socialAssistance)
        ], 201);
    }

    public function show(string $id)
    {
        //
    }

    public function update(SocialAssistanceUpdateRequest $request, SocialAssistance $socialAssistance)
    {
        $this->socialAssistanceService->updateSocialAssistance($request->validated(), $request->file('image') ?? null, $socialAssistance);

        return ResponseHelper::success('Social assistance updated successfully', [
            new SocialAssistanceResource($socialAssistance->fresh())
        ], 200);
    }

    public function destroy(SocialAssistance $socialAssistance)
    {
        $this->socialAssistanceService->deleteSocialAssistance($socialAssistance);

        return ResponseHelper::success('Social assistance deleted successfully', null, 200);
    }
}
