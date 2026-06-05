<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocialAssistanceRecipientStoreRequest;
use App\Http\Requests\SocialAssistanceRecipientUpdateRequest;
use App\Http\Resources\SocialAssistanceRecipientResource;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;
use Illuminate\Http\Request;

class SocialAssistanceRecipientController extends Controller
{
    public function index(SocialAssistance $socialAssistance)
    {
        $recipients = $socialAssistance->socialAssistanceRecipients()
            ->with(['socialAssistance', 'headOfFamily'])
            ->get();

        return ResponseHelper::success(
            'Social assistance recipients retrieved successfully',
            SocialAssistanceRecipientResource::collection($recipients),
            200
        );
    }

    public function store(SocialAssistanceRecipientStoreRequest $request, SocialAssistance $socialAssistance)
    {
        $headOfFamily = $request->user()->headOfFamily;
        abort_unless($headOfFamily, 403);

        $recipient = SocialAssistanceRecipient::create([
            'social_assistance_id' => $socialAssistance->id,
            'head_of_family_id' => $headOfFamily->id,
            ...$request->validated(),
            'status' => 'pending',
        ]);

        $recipient->load(['socialAssistance', 'headOfFamily']);

        return ResponseHelper::success(
            'Social assistance recipient created successfully',
            new SocialAssistanceRecipientResource($recipient),
            200
        );
    }

    public function show(SocialAssistance $socialAssistance, SocialAssistanceRecipient $recipient)
    {
        abort_unless($recipient->social_assistance_id === $socialAssistance->id, 404);

        $recipient->load(['socialAssistance', 'headOfFamily']);

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
        $recipient->load(['socialAssistance', 'headOfFamily']);

        return ResponseHelper::success(
            'Social assistance recipient updated successfully',
            new SocialAssistanceRecipientResource($recipient),
            200
        );
    }

    public function destroy(Request $request, SocialAssistance $socialAssistance, SocialAssistanceRecipient $recipient)
    {
        abort_unless($recipient->social_assistance_id === $socialAssistance->id, 404);
        abort_unless($recipient->head_of_family_id === $request->user()->headOfFamily?->id, 403);

        $recipient->delete();

        return ResponseHelper::success(
            'Social assistance recipient cancelled successfully',
            new SocialAssistanceRecipientResource($recipient),
            200
        );
    }
}
