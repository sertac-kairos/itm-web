<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

$formattedDateTime = date('D M j H:i:s Y');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$remoteAddress = $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'];

// Handle broken pipe errors gracefully when running under concurrently
// Use a more robust approach to handle stdout/stderr issues
$logMessage = "[$formattedDateTime] $remoteAddress [$requestMethod] URI: $uri\n";

// Try multiple approaches to handle logging without broken pipe errors
try {
    // First try: Direct stdout write with error suppression
    @file_put_contents('php://stdout', $logMessage);
} catch (Exception $e) {
    // If that fails, try stderr
    try {
        @file_put_contents('php://stderr', $logMessage);
    } catch (Exception $e) {
        // If both fail, silently continue without logging
        // This prevents the broken pipe error from stopping execution
    }
}

require_once $publicPath.'/index.php';