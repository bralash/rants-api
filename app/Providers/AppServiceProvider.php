<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter; 
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function($request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(20)->by($request->ip());
        });

        // Custom rate limiter for login attempts
        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Public rate limiter
        RateLimiter::for('public', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
