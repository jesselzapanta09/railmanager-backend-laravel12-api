<?php

namespace App\Http\Middleware;

use App\Models\TokenBlacklist;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization', '');

        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Access token required',
            ], 401);
        }

        $token = substr($authHeader, 7);

        try {
            // Check blacklist
            $blacklisted = TokenBlacklist::where('token', $token)->first();
            if ($blacklisted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token has been invalidated. Please login again.',
                ], 401);
            }

            // Verify JWT
            $secret  = env('JWT_SECRET', '');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            // Attach decoded user + raw token to request
            $request->attributes->set('user', (array) $decoded);
            $request->attributes->set('token', $token);

            return $next($request);

        } catch (ExpiredException | SignatureInvalidException | \UnexpectedValueException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token',
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token',
            ], 403);
        }
    }
}
