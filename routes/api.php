<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TeamMemberController;
use App\Http\Controllers\Api\V1\ApiAuthController;
use App\Http\Controllers\Api\V1\ConfessionController;
use App\Http\Controllers\Api\V1\EpisodeController;
use App\Http\Controllers\Api\V1\PlaylistController;
use App\Models\Episode;

/**
 * @OA\Info(
 *     title="Rants and Confessions",
 *     version="1.0.0",
 *     description="API documentation for Rants and Confessions",
 *     @OA\Contact(
 *         email="emmanuelasaber@gmail.com",
 *     )
 * )
 */


Route::prefix('v1')->group(function () {
    // Public routes
    // Auth
    Route::post('register', [ApiAuthController::class, 'register'])->middleware('throttle:public');
    Route::post('login', [ApiAuthController::class, 'login'])->middleware('throttle:login');


    // Confessions
    Route::apiResource('confessions', ConfessionController::class)->only(['store', 'show']);

    // Team Members
    Route::apiResource('team-members', TeamMemberController::class)->only(['index', 'show']);

    // Episodes
    // Route::get('episodes', [EpisodeController::class, 'index']);
    // Route::post('episodes', [EpisodeController::class, 'store']);
    // Route::get('episodes/season/{season}', [EpisodeController::class, 'getEpisodeBySeason']);
    // Route::get('episodes/search', [EpisodeController::class, 'searchEpisodes']);
    // Route::put('episodes/{id}', [EpisodeController::class, 'update']);
    Route::apiResource('episodes', EpisodeController::class)->except(['store','delete','update']);
    Route::apiResource('playlists', PlaylistController::class);
    Route::post('/playlists/{playlist}/episodes', [PlaylistController::class, 'addEpisodesToPlaylist']);

    
    




    // Protected routes
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']); // Logout

        // Admin-only routes
        Route::middleware('check.role:admin')->group(function () {
            Route::apiResource('team-members', TeamMemberController::class)->except(['index', 'show']); 
            Route::apiResource('confessions', ConfessionController::class)->except(['store', 'show']); 
            Route::apiResource('episodes', EpisodeController::class)->only(['store','delete','update']); 
        });
    });  
});



