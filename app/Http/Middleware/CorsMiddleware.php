<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOriginsRaw = env('ALLOWED_ORIGINS', '');
        $allowedOrigins    = array_map('trim', explode(',', $allowedOriginsRaw));
        $origin            = $request->header('Origin', '');

        // Handle pre-flight OPTIONS
        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 200);
            return $this->addCorsHeaders($response, $origin, $allowedOrigins);
        }

        $response = $next($request);

        return $this->addCorsHeaders($response, $origin, $allowedOrigins);
    }

    private function addCorsHeaders(Response $response, string $origin, array $allowedOrigins): Response
    {
        // No origin header = Postman / server-to-server — allow it
        if ($origin === '') {
            return $response
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        if (in_array($origin, $allowedOrigins, true)) {
            return $response
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        // Origin not in allowlist — no CORS headers, browser will block it
        return $response;
    }
}
