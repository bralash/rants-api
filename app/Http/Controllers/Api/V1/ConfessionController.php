<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConfessionResource;
use App\Models\Confession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfessionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/confessions",
     *     summary="Retrieve a list of confessions",
     *     description="Fetch all confessions. Optionally, you can filter by approval status using the `status` query parameter.",
     *     operationId="getConfessions",
     *     tags={"Confessions"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter confessions by approval status. Values: 'approved', 'pending'.",
     *
     *         @OA\Schema(
     *             type="string",
     *             enum={"approved", "pending"}
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of confessions retrieved successfully.",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="message", type="string", example="I ate the last slice of pizza."),
     *                 @OA\Property(property="category", type="string", example="Funny"),
     *                 @OA\Property(property="emotion", type="string", example="Guilty"),
     *                 @OA\Property(property="is_approved", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-26T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-26T12:00:00Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid query parameter.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid status parameter."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="An error occurred while retrieving confessions."
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Confession::query();

        if ($request->has('status')) {
            $status = $request->get('status');

            if (in_array($status, ['approved', 'pending'])) {
                $query->where('is_approved', $status === 'approved');
            } else {
                return response()->json([
                    'error' => 'Invalid status parameter. Use "approved" or "pending".',
                ], 400);
            }
        }

        $confessions = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => ConfessionResource::collection($confessions),
            'meta' => [
                'total' => $confessions->total(),
                'page' => $confessions->currentPage(),
                'last_page' => $confessions->lastPage(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/v1/confessions",
     *     summary="Submit a new confession",
     *     description="Allows users to submit an anonymous confession.",
     *     operationId="storeConfession",
     *     tags={"Confessions"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"message"},
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="The text of the confession.",
     *                 example="I accidentally broke my mom's favorite vase."
     *             ),
     *             @OA\Property(
     *                 property="category",
     *                 type="string",
     *                 description="Optional category of the confession.",
     *                 example="Funny"
     *             ),
     *             @OA\Property(
     *                 property="emotion",
     *                 type="string",
     *                 description="Optional emotion associated with the confession.",
     *                 example="Guilty"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Confession submitted successfully.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Confession created successfully."
     *             ),
     *             @OA\Property(
     *                 property="confession",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="message", type="string", example="I accidentally broke my mom's favorite vase."),
     *                 @OA\Property(property="category", type="string", example="Funny"),
     *                 @OA\Property(property="emotion", type="string", example="Guilty"),
     *                 @OA\Property(property="is_approved", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-26T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-26T10:00:00Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="The message field is required."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="An error occurred while saving the confession."
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'catergory' => 'nullable|string|max:255',
            'emotion' => 'nullable|string|max:255',
        ]);

        $confession = Confession::create([
            'message' => $request->message,
            'category' => $request->category,
            'emotion' => $request->emotion,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Confession added successfully',
            'data' => $confession
        ]);
    }


    /**
     * @OA\Get(
     *     path="/v1/confessions/{id}",
     *     summary="Get a single confession",
     *     description="This endpoint retrieves the details of a specific confession by its ID.",
     *     operationId="getConfession",
     *     tags={"Confessions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the confession to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Confession retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="I feel guilty about not sharing."),
     *                 @OA\Property(property="is_approved", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Confession not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Confession not found.")
     *         )
     *     )
     * )
     */

    public function show(Confession $confession): JsonResponse {
        return response()->json([
            'status' => 'success',
            'data' => new ConfessionResource($confession)
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/v1/confessions/{id}",
     *     summary="Toggle approval of a confession",
     *     description="This endpoint allows you to approve or revoke the approval of a confession.",
     *     operationId="updateConfession",
     *     tags={"Confessions"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the confession to toggle approval for",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Confession approval status toggled successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Confession approved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="I feel guilty about not sharing."),
     *                 @OA\Property(property="is_approved", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Confession not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Confession not found.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $confession = Confession::findOrFail($id);

        $confession->is_approved = !$confession->is_approved;
        $confession->save();

        $status = $confession->is_approved ? 'approved' : 'revoked';

        return response()->json([
            'status' => 'success',
            'message' => `Confession {$status} successfully`,
            'data' => $confession
        ]);
    }

    
    /**
     * @OA\Delete(
     *     path="/v1/confessions/{id}",
     *     summary="Delete a confession",
     *     description="This endpoint deletes a specific confession by its ID.",
     *     operationId="deleteConfession",
     *     tags={"Confessions"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the confession to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Confession deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Confession deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Confession not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Confession not found.")
     *         )
     *     )
     * )
     */

    public function destroy(Confession $confession): JsonResponse
    {
        $confession->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Confession removed successfully'
        ]);
    }
}
