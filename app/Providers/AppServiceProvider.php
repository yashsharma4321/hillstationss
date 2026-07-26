<?php

namespace App\Providers;

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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        $host = request()->getHost();
        $isLocal = in_array($host, ['127.0.0.1', 'localhost', '::1']);

        if (!$isLocal && (str_contains(request()->url(), 'ngrok-free.dev') || config('app.env') === 'production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
