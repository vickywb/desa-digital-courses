<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocialAssistanceRecipientStoreRequest;
use App\Http\Requests\SocialAssistanceRecipientUpdateRequest;
use App\Http\Resources\SocialAssistanceRecipientResource;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;
use App\Services\SocialAssistanceRecipientService;
use Illuminate\Http\Request;

class SocialAssistanceRecipientController extends Controller
{
    public function __construct(
        private SocialAssistanceRecipientService $socialAssistanceRecipientService
    ) {}

    public function index(SocialAssistance $socialAssistance)
    {
        $recipients = $socialAssistance->recipients()
            ->with(['socialAssistance.file', 'headOfFamily.file', 'file'])
            ->get();

        return ResponseHelper::success(
            'Social assistance recipients retrieved successfully',
            SocialAssistanceRecipientResource::collection($recipients),
            200
        );
    }

    public function myRecipients(Request $request)
    {
        $headOfFamily = $request->user()->headOfFamily;
        abort_unless($headOfFamily, 403);

        $recipients = SocialAssistanceRecipient::query()
            ->where('head_of_family_id', $headOfFamily->id)
            ->with(['socialAssistance.file', 'headOfFamily.file', 'file'])
            ->latest()
            ->get();

        return ResponseHelper::success(
            'My social assistance recipients retrieved successfully',
            SocialAssistanceRecipientResource::collection($recipients),
            200
        );
    }

    public function myRecipientDetail(Request $request, SocialAssistanceRecipient $recipient)
    {
        $headOfFamily = $request->user()->headOfFamily;
        abort_unless($headOfFamily, 403);
        abort_unless($recipient->head_of_family_id === $headOfFamily->id, 403);

        $recipient->load(['socialAssistance.file', 'headOfFamily.file', 'file']);

        return ResponseHelper::success(
            'Recipient detail retrieved successfully',
            new SocialAssistanceRecipientResource($recipient),
            200
        );
    }

    public function store(SocialAssistanceRecipientStoreRequest $request, SocialAssistance $socialAssistance)
    {
        $headOfFamily = $request->user()->headOfFamily;
        abort_unless($headOfFamily, 403);

        abort_if(! $socialAssistance->is_available, 422, 'Bantuan sosial ini sudah habis');

        $recipient = $this->socialAssistanceRecipientService->createRecipient(
            $request->validated(),
            $socialAssistance,
            $headOfFamily->id,
            $request->file('proof'),
        );

        return ResponseHelper::success(
            'Social assistance recipient created successfully',
            new SocialAssistanceRecipientResource($recipient),
            200
        );
    }

    public function show(SocialAssistance $socialAssistance, SocialAssistanceRecipient $recipient)
    {
        abort_unless($recipient->social_assistance_id === $socialAssistance->id, 404);

        $recipient->load(['socialAssistance.file', 'headOfFamily.file', 'file']);

        return ResponseHelper::success(
            'Social assistance recipient retrieved successfully',
            new SocialAssistanceRecipientResource($recipient),
            200
        );
    }

    public function update(SocialAssistanceRecipientUpdateRequest $request, SocialAssistance $socialAssistance, SocialAssistanceRecipient $recipient)
    {
        abort_unless($recipient->social_assistance_id === $socialAssistance->id, 404);

        $recipient->fill($request->validated());
        $recipient->save();

        if ($request->status === 'approved') {
            $socialAssistance->update(['is_available' => false]);
        }

        $recipient->load(['socialAssistance.file', 'headOfFamily.file', 'file']);

        return ResponseHelper::success(
            'Social assistance recipient updated successfully',
            new SocialAssistanceRecipientResource($recipient),
            200
        );
    }

    public function allRecipients()
    {
        $recipients = SocialAssistanceRecipient::query()
            ->with(['socialAssistance.file', 'headOfFamily.file', 'file'])
            ->latest()
            ->paginate(20);

        return ResponseHelper::success(
            'All recipients retrieved successfully',
            SocialAssistanceRecipientResource::collection($recipients),
            200
        );
    }

    public function destroy(Request $request, SocialAssistance $socialAssistance, SocialAssistanceRecipient $recipient)
    {
        abort_unless($recipient->social_assistance_id === $socialAssistance->id, 404);
        abort_unless($recipient->head_of_family_id === $request->user()->headOfFamily?->id, 403);

        $this->socialAssistanceRecipientService->deleteRecipient($recipient);

        return ResponseHelper::success(
            'Social assistance recipient cancelled successfully',
            new SocialAssistanceRecipientResource($recipient),
            200
        );
    }
}
