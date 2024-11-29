<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Episode;
use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Http\Resources\EpisodeResource;
use Illuminate\Http\JsonResponse;

class EpisodeController extends Controller
{

    /**
     * @OA\Get(
     *     path="/v1/episodes",
     *     summary="Get paginated episodes",
     *     description="This endpoint retrieves a paginated list of all podcast episodes.",
     *     operationId="getEpisodes",
     *     tags={"Episodes"},
     *     @OA\Response(
     *         response=200,
     *         description="Episodes retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="The Beginning of a Journey"),
     *                     @OA\Property(property="description", type="string", example="An episode about starting something new."),
     *                     @OA\Property(property="img_url", type="string", example="https://example.com/image.jpg"),
     *                     @OA\Property(property="audio_url", type="string", example="https://example.com/audio.mp3"),
     *                     @OA\Property(property="duration", type="string", example="30:15"),
     *                     @OA\Property(property="posted_on", type="string", example="2024-11-25"),
     *                     @OA\Property(property="season", type="integer", example=1),
     *                     @OA\Property(property="episode", type="integer", example=5),
     *                     @OA\Property(property="spotify_url", type="string", example="https://anchor.fm/example"),
     *                     @OA\Property(property="apple_podcasts_url", type="string", example="https://apple.com/example"),
     *                     @OA\Property(property="archive", type="string", example="0"),
     *                     @OA\Property(property="featured", type="string", example="1"),
     *                     @OA\Property(property="slug", type="string", example="the-beginning-of-a-journey"),
     *                     @OA\Property(property="created_at", type="string", example="2024-11-25T10:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2024-11-26T10:00:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=45),
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function index(): JsonResponse {
        $episodes = Episode::paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => EpisodeResource::collection($episodes),
            'meta' => [
                'total' => $episodes->total(),
                'page' => $episodes->currentPage(),
                'last_page' => $episodes->lastPage()
            ]
        ]);
    }


    /**
     * Get episodes by season number.
     *
     * @OA\Get(
     *     path="/v1/episodes/season/{season}",
     *     summary="Get episodes by season number",
     *     description="This endpoint retrieves a paginated list of episodes for a specific season.",
     *     operationId="getEpisodesBySeason",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="season",
     *         in="path",
     *         description="The season number of the episodes",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episodes retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="The Beginning of a Journey"),
     *                     @OA\Property(property="description", type="string", example="An episode about starting something new."),
     *                     @OA\Property(property="img_url", type="string", example="https://example.com/image.jpg"),
     *                     @OA\Property(property="audio_url", type="string", example="https://example.com/audio.mp3"),
     *                     @OA\Property(property="duration", type="string", example="30:15"),
     *                     @OA\Property(property="posted_on", type="string", example="2024-11-25"),
     *                     @OA\Property(property="season", type="integer", example=1),
     *                     @OA\Property(property="episode", type="integer", example=5),
     *                     @OA\Property(property="spotify_url", type="string", example="https://anchor.fm/example"),
     *                     @OA\Property(property="apple_podcasts_url", type="string", example="https://apple.com/example"),
     *                     @OA\Property(property="archive", type="string", example="0"),
     *                     @OA\Property(property="featured", type="string", example="1"),
     *                     @OA\Property(property="slug", type="string", example="the-beginning-of-a-journey"),
     *                     @OA\Property(property="created_at", type="string", example="2024-11-25T10:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2024-11-26T10:00:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=45),
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3)
     *             )
     *         )
     *     )
     * )
     */
    public function getEpisodeBySeason(int $season): JsonResponse {
        $episodes = Episode::where('season', $season)->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => EpisodeResource::collection($episodes),
            'meta' => [
                'total' => $episodes->total(),
                'page' => $episodes->currentPage(),
                'lastPage' => $episodes->lastPage()
            ]
        ]);
    }


    /**
     * @OA\Get(
     *     path="/v1/episodes/search",
     *     summary="Search episodes by title or description",
     *     description="This endpoint allows users to search for episodes based on a search term. It will search both the title and description of the episodes and return matching results.",
     *     operationId="searchEpisodes",
     *     tags={"Episodes"},
     *     
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="The search term to search for in the title or description of episodes.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="podcast"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episodes retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="The Ultimate Podcast"),
     *                     @OA\Property(property="description", type="string", example="An in-depth discussion about podcasting."),
     *                     @OA\Property(property="img_url", type="string", example="https://example.com/image.jpg"),
     *                     @OA\Property(property="audio_url", type="string", example="https://example.com/audio.mp3"),
     *                     @OA\Property(property="duration", type="string", example="45:00"),
     *                     @OA\Property(property="posted_on", type="string", example="2024-11-01"),
     *                     @OA\Property(property="season", type="integer", example=1),
     *                     @OA\Property(property="episode", type="integer", example=5),
     *                     @OA\Property(property="anchor_podcast", type="string", example="https://anchor.fm/example"),
     *                     @OA\Property(property="apple_podcasts", type="string", example="https://apple.com/example"),
     *                     @OA\Property(property="google_podcasts", type="string", example="https://google.com/example"),
     *                     @OA\Property(property="archive", type="string", example="0"),
     *                     @OA\Property(property="featured", type="string", example="1"),
     *                     @OA\Property(property="slug", type="string", example="the-ultimate-podcast")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=10),
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid search term.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The search term is invalid.")
     *         )
     *     )
     * )
     */


    public function searchEpisodes(Request $request): JsonResponse {
        $request->validate([
            'search' => 'required|string|max:255|min:3',
        ]);

        $searchTerm = $request->input('search');

        $episodes = Episode::where('title', 'like', '%' . $searchTerm . '%')
            ->orWhere('description', 'like', '%' . $searchTerm . '%')
            ->distinct()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => EpisodeResource::collection($episodes),
            'meta' => [
                'total' => $episodes->total(),
                'page' => $episodes->currentPage(),
                'last_page' => $episodes->lastPage()
            ],
        ]);
    }


   
    /**
     * @OA\Post(
     *     path="/v1/episodes",
     *     summary="Create a new episode",
     *     description="This endpoint creates a new episode. Only authenticated users can create episodes.",
     *     operationId="storeEpisode",
     *     tags={"Episodes"},
     *     security={{"Bearer": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="The Journey Begins"),
     *             @OA\Property(property="description", type="string", example="The first episode of the series where we explore..."),
     *             @OA\Property(property="img_url", type="string", example="https://example.com/episode1.jpg"),
     *             @OA\Property(property="audio_url", type="string", example="https://example.com/audio/episode1.mp3"),
     *             @OA\Property(property="duration", type="string", example="00:45:30"),
     *             @OA\Property(property="posted_on", type="string", format="date-time", example="2024-11-29T12:00:00Z"),
     *             @OA\Property(property="season", type="integer", example=1),
     *             @OA\Property(property="episode", type="integer", example=1),
     *             @OA\Property(property="spotify_url", type="string", example="https://spotify.com/episode1"),
     *             @OA\Property(property="apple_podcasts_url", type="string", example="https://apple.com/episode1"),
     *             @OA\Property(property="archive", type="string", example="0"),
     *             @OA\Property(property="featured", type="string", example="1"),
     *             @OA\Property(property="slug", type="string", example="the-journey-begins")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Episode created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="The Journey Begins"),
     *                 @OA\Property(property="description", type="string", example="The first episode of the series where we explore..."),
     *                 @OA\Property(property="img_url", type="string", example="https://example.com/episode1.jpg"),
     *                 @OA\Property(property="audio_url", type="string", example="https://example.com/audio/episode1.mp3"),
     *                 @OA\Property(property="duration", type="string", example="00:45:30"),
     *                 @OA\Property(property="posted_on", type="string", format="date-time", example="2024-11-29T12:00:00Z"),
     *                 @OA\Property(property="season", type="integer", example=1),
     *                 @OA\Property(property="episode", type="integer", example=1),
     *                 @OA\Property(property="anchor_podcast", type="string", example="Anchor FM"),
     *                 @OA\Property(property="apple_podcasts", type="string", example="https://apple.com/episode1"),
     *                 @OA\Property(property="google_podcasts", type="string", example="https://google.com/episode1"),
     *                 @OA\Property(property="archive", type="string", example="0"),
     *                 @OA\Property(property="featured", type="string", example="1"),
     *                 @OA\Property(property="slug", type="string", example="the-journey-begins")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input or validation errors.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, token is invalid or missing.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function store(StoreEpisodeRequest $request): JsonResponse {
        $episode = Episode::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new EpisodeResource($episode)
        ], 201);
    }



    /**
     * @OA\Put(
     *     path="/v1/episodes/{id}",
     *     summary="Update an episode",
     *     description="This endpoint updates an episode using the provided data.",
     *     operationId="updateEpisode",
     *     tags={"Episodes"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the episode to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Episode Title"),
     *             @OA\Property(property="description", type="string", example="An updated description of the episode."),
     *             @OA\Property(property="img_url", type="string", format="url", example="https://example.com/updated-image.jpg"),
     *             @OA\Property(property="audio_url", type="string", format="url", example="https://example.com/updated-audio.mp3"),
     *             @OA\Property(property="duration", type="string", example="01:15:30"),
     *             @OA\Property(property="posted_on", type="string", format="date-time", example="2024-12-01T12:00:00Z"),
     *             @OA\Property(property="season", type="integer", example=2),
     *             @OA\Property(property="episode", type="integer", example=5),
     *             @OA\Property(property="spotify_url", type="string", example="https://spotify.com/updated-episode"),
     *             @OA\Property(property="apple_podcasts_url", type="string", example="https://podcasts.apple.com/updated-episode"),
     *             @OA\Property(property="archive", type="string", example="0"),
     *             @OA\Property(property="featured", type="string", example="1"),
     *             @OA\Property(property="slug", type="string", example="updated-episode-title")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Episode Title"),
     *                 @OA\Property(property="description", type="string", example="An updated description of the episode."),
     *                 @OA\Property(property="img_url", type="string", format="url", example="https://example.com/updated-image.jpg"),
     *                 @OA\Property(property="audio_url", type="string", format="url", example="https://example.com/updated-audio.mp3"),
     *                 @OA\Property(property="duration", type="string", example="01:15:30"),
     *                 @OA\Property(property="posted_on", type="string", format="date-time", example="2024-12-01T12:00:00Z"),
     *                 @OA\Property(property="season", type="integer", example=2),
     *                 @OA\Property(property="episode", type="integer", example=5),
     *                 @OA\Property(property="spotify_url", type="string", example="https://spotify.com/updated-episode"),
     *                 @OA\Property(property="apple_podcasts_url", type="string", example="https://podcasts.apple.com/updated-episode"),
     *                 @OA\Property(property="archive", type="string", example="0"),
     *                 @OA\Property(property="featured", type="string", example="1"),
     *                 @OA\Property(property="slug", type="string", example="updated-episode-title"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-29T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-01T12:15:30Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Episode not found.")
     *         )
     *     )
     * )
     */

    public function update(UpdateEpisodeRequest $request, Episode $episode): JsonResponse {
        \Log::info('Authenticated User:', [auth()->user()]);
        \Log::info('Episode:', [$episode]);
        $episode->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Episode updated successfully.',
            'data' => $episode
        ], 200);
    }



    /**
     * @OA\Delete(
     *     path="/v1/episodes/{id}",
     *     summary="Delete an episode",
     *     description="This endpoint deletes a specific episode by its ID.",
     *     operationId="deleteEpisode",
     *     tags={"Episodes"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the episode to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Episode deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Episode not found.")
     *         )
     *     )
     * )
     */
    public function destroy(Episode $episode): JsonResponse {
        $episode->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Episode was deleted successfully',
        ]);
    }
}
