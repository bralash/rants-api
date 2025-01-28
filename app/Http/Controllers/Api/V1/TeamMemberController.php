<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTeamMemberRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;

class TeamMemberController extends Controller
{
    
    /**
     * @OA\Get(
     *     path="/v1/team-members",
     *     summary="Get all team members",
     *     description="This endpoint retrieves a paginated list of all team members.",
     *     operationId="getTeamMembers",
     *     tags={"Team Members"},
     *     @OA\Response(
     *         response=200,
     *         description="Team members retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Clitoria Wettum"),
     *                     @OA\Property(property="role", type="string", example="Host"),
     *                     @OA\Property(property="bio", type="string", example="An old caved woman of many values."),
     *                     @OA\Property(property="profile_image", type="string", example="https://example.com/image.jpg"),
     *                     @OA\Property(property="social_media_links", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="platform", type="string", example="Instagram"),
     *                             @OA\Property(property="url", type="string", example="https://instagram.com/clitoria")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=20),
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=2)
     *             )
     *         )
     *     )
     * )
     */

    public function index(): JsonResponse
    {
        // $members = TeamMember::paginate(6);
        $members = TeamMember::with('socialMediaLinks')->paginate(6);

        return response()->json([
            'status' => 'success',
            'data' => TeamMemberResource::collection($members),
            'meta' => [
                'total' => $members->total(),
                'page' => $members->currentPage(),
                'last_page' => $members->lastPage()
            ]
        ]);
    }

    

    
    
    /**
     * @OA\Post(
     *     path="/v1/team-members",
     *     summary="Create a new team member with social media links",
     *     description="This endpoint creates a new team member and their associated social media links.",
     *     operationId="createTeamMember",
     *     tags={"Team Members"},
     *     security={{"Bearer": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Clitoria Wettum"),
     *             @OA\Property(property="role", type="string", example="Host"),
     *             @OA\Property(property="bio", type="string", example="An experienced podcast host."),
     *             @OA\Property(property="profile_image", type="string", format="url", example="https://example.com/image.jpg"),
     *             @OA\Property(property="social_media_links", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="platform", type="string", example="Instagram"),
     *                     @OA\Property(property="url", type="string", example="https://instagram.com/clitoria")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team member created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Clitoria Wettum"),
     *                 @OA\Property(property="role", type="string", example="Host"),
     *                 @OA\Property(property="bio", type="string", example="An experienced podcast host."),
     *                 @OA\Property(property="profile_image", type="string", example="https://example.com/image.jpg"),
     *                 @OA\Property(property="social_media_links", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="platform", type="string", example="Instagram"),
     *                         @OA\Property(property="url", type="string", example="https://instagram.com/clitoria")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|url',
            'social_media_links' => 'nullable|array',
            'social_media_links.*.platform' => 'required_with:social_media_links|string|max:255',
            'social_media_links.*.url' => 'required_with:social_media_links|url',
        ]);

        // Create the team member
        $teamMember = TeamMember::create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'bio' => $validated['bio'] ?? null,
            'profile_image' => $validated['profile_image'] ?? null,
        ]);

        // Create associated social media links if provided
        if (!empty($validated['social_media_links'])) {
            foreach ($validated['social_media_links'] as $link) {
                $teamMember->socialMediaLinks()->create($link);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => new TeamMemberResource($teamMember->load('socialMediaLinks')),
        ]);
    }



    /**
     * @OA\Get(
     *     path="/v1/team-members/{id}",
     *     summary="Get a single team member",
     *     description="This endpoint retrieves details of a single team member by their ID.",
     *     operationId="getTeamMember",
     *     tags={"Team Members"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the team member to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team member retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Clitoria Wettum"),
     *                 @OA\Property(property="role", type="string", example="Host"),
     *                 @OA\Property(property="bio", type="string", example="An experienced podcast host."),
     *                 @OA\Property(property="profile_image", type="string", example="https://example.com/image.jpg"),
     *                 @OA\Property(property="social_media_links", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="platform", type="string", example="Instagram"),
     *                         @OA\Property(property="url", type="string", example="https://instagram.com/clitoria")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Team member not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Team member not found.")
     *         )
     *     )
     * )
     */
    public function show(TeamMember $teamMember): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new TeamMemberResource($teamMember->load('socialMediaLinks')),
        ]);
    }

    
    /**
     * @OA\Put(
     *     path="/v1/team-members/{id}",
     *     summary="Update a team member's information",
     *     description="This endpoint updates a team member's information, including their social media links.",
     *     operationId="updateTeamMember",
     *     tags={"Team Members"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the team member to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Clitoria Wettum"),
     *             @OA\Property(property="role", type="string", example="Host"),
     *             @OA\Property(property="bio", type="string", example="An experienced podcast host."),
     *             @OA\Property(property="profile_image", type="string", format="url", example="https://example.com/image.jpg"),
     *             @OA\Property(property="social_media_links", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="platform", type="string", example="Instagram"),
     *                     @OA\Property(property="url", type="string", example="https://instagram.com/clitoria")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team member updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Team member updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Clitoria Wettum"),
     *                 @OA\Property(property="role", type="string", example="Host"),
     *                 @OA\Property(property="bio", type="string", example="An experienced podcast host."),
     *                 @OA\Property(property="profile_image", type="string", example="https://example.com/image.jpg"),
     *                 @OA\Property(property="social_media_links", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="platform", type="string", example="Instagram"),
     *                         @OA\Property(property="url", type="string", example="https://instagram.com/clitoria")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Team member not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Team member not found.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, TeamMember $teamMember): JsonResponse
    {
        // Validate incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|url',
            'social_media_links' => 'nullable|array',
            'social_media_links.*.platform' => 'required_with:social_media_links|string|max:255',
            'social_media_links.*.url' => 'required_with:social_media_links|url',
        ]);

        // Update team member details
        $teamMember->update([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'bio' => $validated['bio'] ?? $teamMember->bio,  // Keep existing bio if not updated
            'profile_image' => $validated['profile_image'] ?? $teamMember->profile_image,  // Keep existing image if not updated
        ]);

        // Update or create social media links
        if (isset($validated['social_media_links'])) {
            foreach ($validated['social_media_links'] as $socialMediaData) {
                $teamMember->socialMediaLinks()->updateOrCreate(
                    ['platform' => $socialMediaData['platform']],  // Find by platform name
                    ['url' => $socialMediaData['url']]  // Update the URL if the platform exists
                );
            }
        }

        // Return success response with updated data
        return response()->json([
            'status' => 'success',
            'message' => 'Team member updated successfully',
            'data' => new TeamMemberResource($teamMember)
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/v1/team-members/{id}",
     *     summary="Delete a team member and their associated social media links",
     *     description="This endpoint deletes a team member along with their associated social media links.",
     *     operationId="deleteTeamMember",
     *     tags={"Team Members"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the team member to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team member and their social media links deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Team member deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Team member not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Team member not found.")
     *         )
     *     )
     * )
     */
    public function destroy(TeamMember $teamMember): JsonResponse
    {
        $teamMember->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Team member removed successfully'
        ]);
    }
}
