<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TeamMemberController;
use App\Http\Controllers\Api\V1\ApiAuthController;

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

    // Protected routes
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']);

        // Admin only routes
        Route::middleware('check.role:admin')->group(function () {
            Route::apiResource('team-members', TeamMemberController::class);
        });

        // Manager can view and update
        Route::middleware('check.role:admin,manager')->group(function () {
            Route::get('team-members', [TeamMemberController::class, 'index']);
            Route::get('team-members/{teamMember}', [TeamMemberController::class, 'show']);
            Route::put('team-members/{teamMember}', [TeamMemberController::class, 'update']);
        });

        // Regular users can only view
        Route::middleware('check.role:admin,manager,user')->group(function () {
            Route::get('team-members', [TeamMemberController::class, 'index']);
            Route::get('team-members/{teamMember}', [TeamMemberController::class, 'show']);
        });
    });
});

// Sample URL
Route::group([
    'domain' => env('APP_URL')], function() {
        Route::get('team-members', function() {
            return 'Hello World';
        });
    }
);
