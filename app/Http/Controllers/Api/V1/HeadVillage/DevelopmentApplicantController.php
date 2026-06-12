<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\DevelopmentApplicantResource;
use App\Models\Development;
use App\Models\DevelopmentApplicant;
use Illuminate\Http\Request;

class DevelopmentApplicantController extends Controller
{
    public function index(Development $development)
    {
        $applicants = $development->applicants()
            ->with(['development.file', 'user.headOfFamily'])
            ->get();

        return ResponseHelper::success(
            'Development applicants retrieved successfully',
            DevelopmentApplicantResource::collection($applicants),
            200
        );
    }

    public function update(Request $request, Development $development, DevelopmentApplicant $applicant)
    {
        abort_unless($applicant->development_id === $development->id, 404);

        $request->validate([
            'status' => ['required', 'string', 'in:approved,rejected'],
        ]);

        $applicant->update([
            'status' => $request->status,
        ]);

        $applicant->load(['development.file', 'user.headOfFamily']);

        return ResponseHelper::success(
            'Development applicant updated successfully',
            new DevelopmentApplicantResource($applicant),
            200
        );
    }

    public function myApplicants(Request $request)
    {
        $applicants = DevelopmentApplicant::query()
            ->where('user_id', $request->user()->id)
            ->with(['development.file', 'user'])
            ->latest()
            ->get();

        return ResponseHelper::success(
            'My development applicants retrieved successfully',
            DevelopmentApplicantResource::collection($applicants),
            200
        );
    }

    public function store(Request $request, Development $development)
    {
        $headOfFamily = $request->user()->headOfFamily;
        abort_unless($headOfFamily, 403);

        $existing = DevelopmentApplicant::query()
            ->where('development_id', $development->id)
            ->where('user_id', $request->user()->id)
            ->where('status', '!=', 'rejected')
            ->first();

        abort_if($existing, 409, 'Kamu sudah mendaftar pembangunan ini');

        abort_if($development->status !== 'planned', 422, 'Pembangunan ini sudah tidak menerima pendaftaran');

        $applicant = DevelopmentApplicant::create([
            'development_id' => $development->id,
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        $applicant->load(['development.file', 'user']);

        return ResponseHelper::success(
            'Development applicant created successfully',
            new DevelopmentApplicantResource($applicant),
            200
        );
    }

    public function show(Development $development, DevelopmentApplicant $applicant)
    {
        abort_unless($applicant->development_id === $development->id, 404);

        $applicant->load(['development.file', 'user']);

        return ResponseHelper::success(
            'Development applicant retrieved successfully',
            new DevelopmentApplicantResource($applicant),
            200
        );
    }

    public function destroy(Request $request, Development $development, DevelopmentApplicant $applicant)
    {
        abort_unless($applicant->development_id === $development->id, 404);
        abort_unless($applicant->user_id === $request->user()->id, 403);

        $applicant->delete();

        return ResponseHelper::success(
            'Development applicant deleted successfully',
            new DevelopmentApplicantResource($applicant),
            200
        );
    }
}
