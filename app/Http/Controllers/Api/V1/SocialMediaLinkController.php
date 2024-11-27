<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialMediaLink;
use App\Http\Resources\SocialMediaLinkResource;
use Illuminate\Http\JsonResponse;

class SocialMediaLinkController extends Controller
{
    public function index(): JsonResponse {
        $links= SocialMediaLink::with('teamMember')->paginate(4);

        return response()->json([
            'status' => 'success',
            'data' => SocialMediaLinkResource::collection($links),
            'meta' => [
                'total' => $links->total(),
                'page' => $links->currentPage(),
                'last_page' => $links->lastPage()
            ]
        ]);
    }

    public function store(Request $request): JsonResponse {
        
        $validated = $request->validate([
            'team_member_id' => 'required|exists:team_members,id',
            'platform' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        $link = SocialMediaLink::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => new SocialMediaLinkResource($link),
        ]);
    }

    public function show(SocialMediaLink $socialMediaLink)
    {
        return response()->json([
            'status' => 'success',
            'data' => new SocialMediaLinkResource($socialMediaLink->load('teamMember')),
        ]);
    }

    public function update(Request $request, SocialMediaLink $socialMediaLink)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        $socialMediaLink->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => new SocialMediaLinkResource($socialMediaLink),
        ]);
    }

    public function destroy(SocialMediaLink $socialMediaLink)
    {
        $socialMediaLink->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Social media link deleted successfully.',
        ]);
    }
}
