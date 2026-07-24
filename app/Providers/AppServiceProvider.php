<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

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
        $this->ensureStorageLinkExists();

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

    /**
     * Ensure public/storage symbolic link exists and is not a physical directory.
     */
    protected function ensureStorageLinkExists(): void
    {
        $publicStoragePath = public_path('storage');
        $targetPath = storage_path('app/public');

        $targetReal = realpath($targetPath);
        $publicReal = (file_exists($publicStoragePath) || is_link($publicStoragePath)) ? realpath($publicStoragePath) : false;

        $isValidLink = $publicReal !== false && $targetReal !== false && $publicReal === $targetReal;

        if (!$isValidLink) {
            if (file_exists($publicStoragePath) || is_link($publicStoragePath)) {
                $linkTarget = @readlink($publicStoragePath);
                if ($linkTarget !== false && realpath($linkTarget) === $targetReal) {
                    return;
                }

                // If unlink/rmdir fail (because it's a physical non-empty directory), delete the physical directory
                if (!@unlink($publicStoragePath) && !@rmdir($publicStoragePath)) {
                    if (is_dir($publicStoragePath)) {
                        File::deleteDirectory($publicStoragePath);
                    }
                }
            }

            try {
                Artisan::call('storage:link');
            } catch (\Throwable $e) {
                // Prevent boot failures if Artisan environment is restricted
            }
        }
    }
}
