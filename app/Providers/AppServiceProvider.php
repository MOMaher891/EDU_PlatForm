<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            try {
                $settings = Setting::getCached();
            } catch (\Throwable $e) {
                // In case migrations haven't run yet
                $settings = (object) [
                    'block_devtools' => false,
                    'block_copy_text' => false,
                ];
            }
            $view->with('appSettings', $settings);
        });
    }
}
