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
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $members = TeamMember::paginate(6);

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
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamMemberRequest $request): JsonResponse
    {
        $member = TeamMember::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Team member added successfully',
            'data' => new TeamMemberResource($member)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamMember $teamMember): JsonResponse
    {
        return response()->json([
            'status' => 'sucess',
            'data' => new TeamMemberResource($teamMember)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTeamMemberRequest $request, TeamMember $teamMember): JsonResponse
    {
        $teamMember->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Team member updated successfully',
            'data' => new TeamMemberResource($teamMember)
        ]);
    }

    /**
     * Remove the specified resource from storage.
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
