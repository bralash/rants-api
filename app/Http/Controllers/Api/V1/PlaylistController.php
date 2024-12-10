<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Playlist;
use App\Services\PlaylistService;
use Illuminate\Http\JsonResponse; 

class PlaylistController extends Controller
{
    protected $playlistService;

    /**
     * Inject PlaylistService.
     *
     * @param PlaylistService $playlistService
     */
    public function __construct(PlaylistService $playlistService) {
        $this->playlistService = $playlistService;
    }



    /**
     * @OA\Get(
     *     path="/v1/playlists",
     *     summary="Get all playlists",
     *     description="This endpoint retrieves a paginated list of all playlists.",
     *     operationId="getPlaylists",
     *     tags={"Playlists"},
     *     @OA\Response(
     *         response=200,
     *         description="Playlists retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Playlist One"),
     *                     @OA\Property(property="description", type="string", example="This is a sample playlist."),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-01T12:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-01T12:00:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=5),
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */

    public function index() {
        $playlists = Playlist::with('episodes')->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $playlists,
        ]);
    }


    /**
     * @OA\Post(
     *     path="/v1/playlists",
     *     summary="Create a new playlist",
     *     description="This endpoint allows users to create a new playlist.",
     *     operationId="createPlaylist",
     *     tags={"Playlists"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="My Playlist"),
     *             @OA\Property(property="description", type="string", example="A playlist of my favorite episodes.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Playlist created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="My Playlist"),
     *                 @OA\Property(property="description", type="string", example="A playlist of my favorite episodes."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-01T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-01T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error.")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'episodes' => 'nullable|array',
            'episodes.*' => 'exists:episodes,id',
        ]);

        $playlist = $this->playlistService->createPlaylist($validated);

        return response()->json([
            'status' => 'success',
            'data' => $playlist->load('episodes'),
        ], 201);
    }



    /**
     * @OA\Post(
     *     path="/v1/playlists/{id}/episodes",
     *     summary="Add episodes to a playlist",
     *     description="This endpoint adds one or more episodes to a playlist.",
     *     operationId="addEpisodesToPlaylist",
     *     tags={"Playlists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the playlist",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="episode_ids", type="array",
     *                 @OA\Items(type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episodes added to playlist successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Episodes added successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="playlist_id", type="integer", example=1),
     *                 @OA\Property(property="added_episode_ids", type="array",
     *                     @OA\Items(type="integer", example=5)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Playlist or episode not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Playlist or episode not found.")
     *         )
     *     )
     * )
     */

    public function addEpisodesToPlaylist(Request $request, Playlist $playlist): JsonResponse {
        $validated = $request->validate([
            'episode_ids' => 'required|array|min:1',
            'episode_ids.*' => 'integer|exists:episodes,id',
        ]);

        $playlist->episodes()->syncWithoutDetaching($validated['episode_ids']);

        $updatedPlaylist = $playlist->load('episodes');

        return response()->json([
            'status' => 'success',
            'message' => 'Episodes added to playlist successfully.',
            'data' => $updatedPlaylist,
        ],200);
    }


    /**
     * @OA\Get(
     *     path="/v1/playlists/{id}",
     *     summary="Get a single playlist",
     *     description="This endpoint retrieves a single playlist by its ID.",
     *     operationId="getPlaylistById",
     *     tags={"Playlists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the playlist",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Playlist retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Playlist One"),
     *                 @OA\Property(property="description", type="string", example="This is a sample playlist."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-01T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-01T12:00:00Z"),
     *                 @OA\Property(property="episodes", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="title", type="string", example="The Journey Begins"),
     *                         @OA\Property(property="description", type="string", example="The first episode of the series."),
     *                         @OA\Property(property="duration", type="string", example="00:45:30"),
     *                         @OA\Property(property="posted_on", type="string", format="date-time", example="2024-12-01T12:00:00Z")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Playlist not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Playlist not found.")
     *         )
     *     )
     * )
     */

    public function show(Playlist $playlist)
    {
        return response()->json([
            'status' => 'success',
            'data' => $playlist->load('episodes'),
        ]);
    }



    /**
     * @OA\Put(
     *     path="/v1/playlists/{id}",
     *     summary="Update an existing playlist",
     *     description="This endpoint updates the details of an existing playlist.",
     *     operationId="updatePlaylist",
     *     tags={"Playlists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the playlist to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Playlist Name"),
     *             @OA\Property(property="description", type="string", example="An updated description for the playlist.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Playlist updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Playlist Name"),
     *                 @OA\Property(property="description", type="string", example="An updated description for the playlist."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-01T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-01T12:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Playlist not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Playlist not found.")
     *         )
     *     )
     * )
     */

    public function update(Request $request, Playlist $playlist)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'episodes' => 'nullable|array',
            'episodes.*' => 'exists:episodes,id',
        ]);

        $playlist = $this->playlistService->updatePlaylist($playlist, $validated);

        return response()->json([
            'status' => 'success',
            'data' => $playlist->load('episodes'),
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/v1/playlists/{id}",
     *     summary="Delete a playlist",
     *     description="This endpoint deletes a playlist by its ID.",
     *     operationId="deletePlaylist",
     *     tags={"Playlists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the playlist to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Playlist deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Playlist deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Playlist not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Playlist not found.")
     *         )
     *     )
     * )
     */

    public function destroy(Playlist $playlist)
    {
        $this->playlistService->deletePlaylist($playlist);

        return response()->json(['status' => 'success'], 204);
    }
}
