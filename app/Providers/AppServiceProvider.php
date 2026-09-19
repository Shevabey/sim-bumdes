<?php

namespace App\Providers;

use App\Models\Feedback;
use App\Observers\FeedbackObserver;
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
        // FR-34: Auto-kirim notifikasi saat Feedback baru dibuat
        Feedback::observe(FeedbackObserver::class);

        // SRS NFR Keamanan: Rate limiting login 5x percobaan per menit per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->input('email') . '|' . $request->ip());
        });
    }
}
