<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\InstructorMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\SectionAccessMiddleware;
use App\Http\Middleware\HandleErrors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware
        $middleware->append(HandleErrors::class);

        // Route middleware aliases
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'instructor' => InstructorMiddleware::class,
            'student' => StudentMiddleware::class,
            'section.access' => SectionAccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();


