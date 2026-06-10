<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFamilyMemberRequest;
use App\Http\Requests\UpdateFamilyMemberRequest;
use App\Http\Resources\FamilyMemberResource;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;

class FamilyMemberController extends Controller
{
    public function index(HeadOfFamily $headFamily)
    {
        $members = $headFamily->familyMembers()
            ->with(['file', 'headOfFamily'])
            ->get();

        return ResponseHelper::success(
            'Family members retrieved successfully',
            FamilyMemberResource::collection($members),
            200
        );
    }

    public function store(StoreFamilyMemberRequest $request, HeadOfFamily $headFamily)
    {
        $member = $headFamily->familyMembers()->create($request->validated());
        $member->load(['file', 'headOfFamily']);

        return ResponseHelper::success(
            'Family member created successfully',
            new FamilyMemberResource($member),
            201
        );
    }

    public function show(HeadOfFamily $headFamily, FamilyMember $member)
    {
        abort_unless($member->head_of_family_id === $headFamily->id, 404);

        $member->load(['file', 'headOfFamily']);

        return ResponseHelper::success(
            'Family member retrieved successfully',
            new FamilyMemberResource($member),
            200
        );
    }

    public function update(UpdateFamilyMemberRequest $request, HeadOfFamily $headFamily, FamilyMember $member)
    {
        abort_unless($member->head_of_family_id === $headFamily->id, 404);

        $member->update($request->validated());
        $member->refresh()->load(['file', 'headOfFamily']);

        return ResponseHelper::success(
            'Family member updated successfully',
            new FamilyMemberResource($member),
            200
        );
    }

    public function destroy(HeadOfFamily $headFamily, FamilyMember $member)
    {
        abort_unless($member->head_of_family_id === $headFamily->id, 404);

        $member->delete();

        return ResponseHelper::success(
            'Family member deleted successfully',
            null,
            200
        );
    }
}
