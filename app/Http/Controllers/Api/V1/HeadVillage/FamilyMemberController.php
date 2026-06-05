<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\FamilyMemberUpdateRequest;
use App\Http\Resources\FamilyMemberResource;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        // not used for head village
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

    public function update(FamilyMemberUpdateRequest $request, HeadOfFamily $headFamily, FamilyMember $member)
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

    public function destroy(string $id)
    {
        // not used for head village
    }
}
