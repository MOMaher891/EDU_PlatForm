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
                    'platform_name' => 'A+ Academy',
                    'platform_logo' => null,
                    'support_email' => 'momaher1588@gmail.com',
                    'support_phone' => '+201113050566',
                    'platform_description' => 'منصة تعليمية متكاملة تقدم دورات تعليمية عالية الجودة',
                ];
            }
            $view->with('appSettings', $settings);
        });
    }
}
