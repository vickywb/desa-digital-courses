<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateHeadOfFamilyRequest;
use App\Http\Resources\HeadOfFamilyResource;
use App\Models\HeadOfFamily;
use Illuminate\Http\Request;

class HeadOfFamilyController extends Controller
{
    public function index()
    {
        $headOfFamilies = HeadOfFamily::with('file')->withCount('familyMembers')->get();

        return ResponseHelper::success(
            'Head of families retrieved successfully',
            HeadOfFamilyResource::collection($headOfFamilies),
            200
        );
    }

    public function store(Request $request)
    {
        // not used for head village
    }

    public function show(HeadOfFamily $headFamily)
    {
        $headFamily->load('file');

        return ResponseHelper::success(
            'Head of family retrieved successfully',
            new HeadOfFamilyResource($headFamily),
            200
        );
    }

    public function update(UpdateHeadOfFamilyRequest $request, HeadOfFamily $headFamily)
    {
        $headFamily->update($request->validated());
        $headFamily->refresh()->load('file');

        return ResponseHelper::success(
            'Head of family updated successfully',
            new HeadOfFamilyResource($headFamily),
            200
        );
    }

    public function destroy(string $id)
    {
        // not used for head village
    }
}
