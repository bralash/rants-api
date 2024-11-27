<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TeamMemberController;
use App\Http\Controllers\Api\V1\ApiAuthController;
use App\Http\Controllers\Api\V1\ConfessionController;

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
    Route::post('register', [ApiAuthController::class, 'register'])->middleware('throttle:public');
    Route::post('login', [ApiAuthController::class, 'login'])->middleware('throttle:login');

    Route::get('confessions', [ConfessionController::class, 'index']); // View confessions (public)
    Route::post('confessions', [ConfessionController::class, 'store']); // Submit confessions (public)
    Route::get('confessions/{confession}', [ConfessionController::class, 'show']); // View a single confession (public)

    // Route::apiResource('team-members', [TeamMemberController::class]);
    Route::get('team-members', [TeamMemberController::class, 'index']);
    Route::post('team-members', [TeamMemberController::class, 'store']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']); // Logout

        // Admin-only routes
        Route::middleware('check.role:admin')->group(function () {
            // Route::apiResource('team-members', TeamMemberController::class); // Admin can manage team members
            Route::apiResource('confessions', ConfessionController::class)->except(['index', 'store', 'show']); // Admin can manage all confessions (update, delete, etc.)
        });

        // User-specific routes (admin and user roles)
        // Route::middleware('check.role:admin,user')->group(function () {
        //     Route::get('team-members', [TeamMemberController::class, 'index']); // Users can view team members
        //     Route::get('team-members/{teamMember}', [TeamMemberController::class, 'show']); // Users can view a specific team member
        // });
    });  
});



