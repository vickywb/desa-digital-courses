<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\FamilyMember;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFamilyMemberRequest;
use App\Http\Requests\UpdateFamilyMemberRequest;
use App\Http\Resources\FamilyMemberResource;
use App\Models\FamilyMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeadOfFamilyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $headOfFamily = $request->user()->headOfFamily;

        abort_unless($headOfFamily, 403);

        $members = $headOfFamily->familyMembers()
            ->with(['file', 'headOfFamily'])
            ->paginate(20);

        return ResponseHelper::success(
            'Family members retrieved successfully',
            FamilyMemberResource::collection($members),
            200
        );
    }

    public function show(Request $request, FamilyMember $familyMember): JsonResponse
    {
        $headOfFamily = $request->user()->headOfFamily;

        abort_unless($headOfFamily, 403);
        abort_unless($familyMember->head_of_family_id === $headOfFamily->id, 404);

        $familyMember->load(['file', 'headOfFamily']);

        return ResponseHelper::success(
            'Family member retrieved successfully',
            new FamilyMemberResource($familyMember),
            200
        );
    }

    public function store(StoreFamilyMemberRequest $request): JsonResponse
    {
        $headOfFamily = $request->user()->headOfFamily;

        abort_unless($headOfFamily, 403);

        $member = $headOfFamily->familyMembers()->create($request->validated());
        $member->load(['file', 'headOfFamily']);

        return ResponseHelper::success(
            'Family member created successfully',
            new FamilyMemberResource($member),
            201
        );
    }

    public function update(UpdateFamilyMemberRequest $request, FamilyMember $familyMember): JsonResponse
    {
        $headOfFamily = $request->user()->headOfFamily;

        abort_unless($headOfFamily, 403);
        abort_unless($familyMember->head_of_family_id === $headOfFamily->id, 404);

        $familyMember->update($request->validated());
        $familyMember = $familyMember->fresh(['file', 'headOfFamily']);

        return ResponseHelper::success(
            'Family member updated successfully',
            new FamilyMemberResource($familyMember),
            200
        );
    }

    public function destroy(Request $request, FamilyMember $familyMember): JsonResponse
    {
        $headOfFamily = $request->user()->headOfFamily;

        abort_unless($headOfFamily, 403);
        abort_unless($familyMember->head_of_family_id === $headOfFamily->id, 404);

        $familyMember->delete();

        return ResponseHelper::success(
            'Family member deleted successfully',
            null,
            200
        );
    }
}
