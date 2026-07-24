<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class StorageLinkTest extends TestCase
{
    public function test_storage_app_public_directory_exists()
    {
        $this->assertDirectoryExists(storage_path('app/public'));
    }

    public function test_public_storage_points_to_storage_app_public()
    {
        $publicStoragePath = public_path('storage');
        $targetPath = storage_path('app/public');

        $this->assertTrue(file_exists($publicStoragePath) || is_link($publicStoragePath), 'public/storage should exist');
        $this->assertEquals(
            realpath($targetPath),
            realpath($publicStoragePath),
            'public/storage must point to storage/app/public'
        );
    }

    public function test_app_service_provider_recreates_symlink_if_missing_or_physical_dir()
    {
        $publicStoragePath = public_path('storage');
        $targetPath = storage_path('app/public');

        // Safely remove existing link/junction without deleting target directory contents
        if (file_exists($publicStoragePath) || is_link($publicStoragePath)) {
            if (!@unlink($publicStoragePath) && !@rmdir($publicStoragePath)) {
                if (is_dir($publicStoragePath) && readlink($publicStoragePath) === false) {
                    File::deleteDirectory($publicStoragePath);
                }
            }
        }

        // Create a physical directory at public/storage
        File::makeDirectory($publicStoragePath, 0755, true, true);
        File::put($publicStoragePath . '/test_file.txt', 'physical directory content');

        $this->assertNotEquals(
            realpath($targetPath),
            realpath($publicStoragePath),
            'Before boot fix, public/storage should be a physical directory distinct from target'
        );

        // Invoke service provider boot to test automatic detection & resolution
        $provider = new \App\Providers\AppServiceProvider($this->app);
        $provider->boot();

        $this->assertTrue(file_exists($publicStoragePath) || is_link($publicStoragePath), 'public/storage should exist after boot fix');
        $this->assertEquals(
            realpath($targetPath),
            realpath($publicStoragePath),
            'After boot fix, public/storage should point to storage/app/public'
        );
    }
}
