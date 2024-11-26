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

    Route::get('confessions',[ConfessionController::class, 'index']);
    Route::post('confessions',[ConfessionController::class, 'store']);
    Route::get('confessions/{confession}',[ConfessionController::class, 'show']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']);

        // Admin only routes
        Route::middleware('check.role:admin')->group(function () {
            Route::apiResource('team-members', TeamMemberController::class);
            // Route::apiResource('confessions', ConfessionController::class);
        });

        // Regular users can only view
        Route::middleware('check.role:admin,user')->group(function () {
            Route::get('team-members', [TeamMemberController::class, 'index']);
            Route::get('team-members/{teamMember}', [TeamMemberController::class, 'show']);
        });
    });  
});

// Sample URL
