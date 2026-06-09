<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\VillageProfileStoreRequest;
use App\Http\Requests\VillageProfileUpdateRequest;
use App\Http\Resources\VillageProfileResource;
use App\Models\VillageProfile;
use App\Services\VillageProfileService;

class VillageProfileController extends Controller
{
    public function __construct(private VillageProfileService $villageProfileService) {}

    public function index()
    {
        $villageProfile = VillageProfile::with('files')->latest()->first();

        if (! $villageProfile) {
            return ResponseHelper::success('No village profile found', null, 200);
        }

        return ResponseHelper::success(
            'Village profile retrieved successfully',
            new VillageProfileResource($villageProfile),
            200
        );
    }

    public function show(VillageProfile $villageProfile)
    {
        $villageProfile->load('files');

        return ResponseHelper::success(
            'Village profile retrieved successfully',
            new VillageProfileResource($villageProfile),
            200
        );
    }

    public function store(VillageProfileStoreRequest $request)
    {
        $villageProfile = $this->villageProfileService->createVillageProfile($request->validated(), $request->file('images', []));

        return ResponseHelper::success('Village Profile successfully created', [
            new VillageProfileResource($villageProfile),
        ], 201);
    }

    public function update(VillageProfileUpdateRequest $request, VillageProfile $villageProfile)
    {
        $villageProfile = $this->villageProfileService->updateVillageProfile($villageProfile, $request->validated(), $request->file('images', []));

        return ResponseHelper::success('Village Profile successfully updated', [
            new VillageProfileResource($villageProfile),
        ], 200);
    }

    public function destroy(VillageProfile $villageProfile)
    {
        $villageProfile = $this->villageProfileService->deleteVillageProfile($villageProfile);

        return ResponseHelper::success('Village Profile successfully deleted', [
            new VillageProfileResource($villageProfile),
        ], 200);
    }
}
