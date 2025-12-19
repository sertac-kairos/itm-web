<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerifyAppSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow disabling via env for local/dev
        if (!filter_var(env('APP_SIGNATURE_ENABLED', false), FILTER_VALIDATE_BOOL)) {
            return $next($request);
        }

        $appKey = $request->header('X-App-Key');
        $signature = $request->header('X-App-Signature');
        $timestamp = $request->header('X-App-Timestamp');
        $nonce = $request->header('X-App-Nonce');

        if (!$appKey || !$signature || !$timestamp || !$nonce) {
            return response()->json(['success' => false, 'message' => 'Missing signature headers'], 401);
        }

        $configuredKey = env('APP_SIGNATURE_KEY');
        $secret = env('APP_SIGNATURE_SECRET');

        if (!$configuredKey || !$secret || !hash_equals($configuredKey, $appKey)) {
            return response()->json(['success' => false, 'message' => 'Invalid app key'], 401);
        }

        // Timestamp window check (default 5 minutes)
        $allowedDrift = (int) env('APP_SIGNATURE_ALLOWED_DRIFT_SECONDS', 300);
        if (abs(time() - (int) $timestamp) > $allowedDrift) {
            return response()->json(['success' => false, 'message' => 'Signature timestamp expired'], 401);
        }

        // Replay protection by nonce
        $nonceCacheKey = 'api_sig_nonce:'.$nonce;
        if (Cache::has($nonceCacheKey)) {
            return response()->json(['success' => false, 'message' => 'Replay detected'], 401);
        }

        $body = $request->getContent() ?? '';
        $bodyHash = hash('sha256', $body);
        $stringToSign = strtoupper($request->getMethod()) . "\n"
            . $request->getPathInfo() . "\n"
            . $timestamp . "\n"
            . $nonce . "\n"
            . $bodyHash;

        $expected = base64_encode(hash_hmac('sha256', $stringToSign, $secret, true));

        if (!hash_equals($expected, $signature)) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }

        // Store nonce to prevent replay within the window
        Cache::put($nonceCacheKey, true, now()->addSeconds($allowedDrift));

        return $next($request);
    }
}


