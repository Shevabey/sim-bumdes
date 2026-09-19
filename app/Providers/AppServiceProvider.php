<?php

namespace App\Providers;

use App\Models\Feedback;
use App\Observers\FeedbackObserver;
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
    }
}
