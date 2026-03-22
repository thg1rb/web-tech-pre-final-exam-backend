<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function(Request $request) {
            // 60 times per 1 minute;
            // Logged in user classify by User ID
            // Not logged in user classify by IP Address
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip);
        });
    }
}
