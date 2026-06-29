<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\KasRequest;
use App\Http\Resources\KasResource;
use App\Models\VillageProfile;
use App\Services\KasService;

class KasController extends Controller
{
    public function __construct(private KasService $kasService) {}

    public function index()
    {
        $villageProfile = VillageProfile::with('kas')->latest()->first();

        if (! $villageProfile || ! $villageProfile->kas) {
            return ResponseHelper::success('No kas data found', null, 200);
        }

        return ResponseHelper::success(
            'Kas retrieved successfully',
            new KasResource($villageProfile->kas),
            200
        );
    }

    public function update(KasRequest $request)
    {
        $villageProfile = VillageProfile::latest()->first();

        if (! $villageProfile) {
            return ResponseHelper::error('No village profile found. Create a village profile first.', null, 404);
        }

        $kas = $this->kasService->createOrUpdateKas($villageProfile, $request->validated());

        return ResponseHelper::success('Kas updated successfully', new KasResource($kas), 200);
    }
}
