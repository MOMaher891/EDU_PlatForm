<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GzipCompressMiddleware
{
    /**
     * Handle an incoming request and compress response with Gzip.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Set Cache-Control headers for performance
        if (!$response->headers->has('Cache-Control')) {
            $response->headers->set('Cache-Control', 'public, max-age=86400, s-maxage=86400');
        }

        // Apply Gzip compression if supported by browser and PHP gzencode exists
        if ($request->header('Accept-Encoding') && str_contains($request->header('Accept-Encoding'), 'gzip') && function_exists('gzencode')) {
            $content = $response->getContent();
            if ($content && is_string($content) && strlen($content) > 500 && !$response->headers->has('Content-Encoding')) {
                $compressed = gzencode($content, 6);
                if ($compressed !== false) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Content-Length', (string) strlen($compressed));
                    $response->headers->set('Vary', 'Accept-Encoding');
                }
            }
        }

        return $response;
    }
}
