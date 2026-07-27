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
     * Ensure public symbolic links (storage, media) exist and are not physical directories.
     */
    protected function ensureStorageLinkExists(): void
    {
        $links = config('filesystems.links', [
            public_path('storage') => storage_path('app/public'),
            public_path('media') => env('UPLOADS_PATH', storage_path('app/public/media')),
        ]);

        foreach ($links as $link => $target) {
            $this->createLinkIfNeeded($link, $target);
        }
    }

    protected function createLinkIfNeeded(string $linkPath, string $targetPath): void
    {
        if (!file_exists($targetPath)) {
            File::makeDirectory($targetPath, 0755, true, true);
        }

        $targetReal = realpath($targetPath);
        $linkReal = (file_exists($linkPath) || is_link($linkPath)) ? realpath($linkPath) : false;

        $isValidLink = $linkReal !== false && $targetReal !== false && $linkReal === $targetReal;

        if (!$isValidLink) {
            if (file_exists($linkPath) || is_link($linkPath)) {
                $linkTarget = @readlink($linkPath);
                if ($linkTarget !== false && realpath($linkTarget) === $targetReal) {
                    return;
                }

                if (!@unlink($linkPath) && !@rmdir($linkPath)) {
                    if (is_dir($linkPath)) {
                        File::deleteDirectory($linkPath);
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
