<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HandleErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            // Log the error
            Log::error('Unhandled error in middleware: ' , [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            // If it's an AJAX request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'حدث خطأ في النظام. يرجى المحاولة مرة أخرى.',
                    'message' => config('app.debug') ? $e->getMessage() : 'System error occurred'
                ], 500);
            }

            // For regular requests, redirect back with error message
            return redirect()->back()->with('error', 'حدث خطأ في النظام. يرجى المحاولة مرة أخرى.');
        }
    }
}
