<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // Wajib di-import

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
        // Paksa Laravel menggunakan HTTPS saat di Vercel (Mode Production)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}