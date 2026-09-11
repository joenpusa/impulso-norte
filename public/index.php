<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Auto-clear stale cache if our new route is missing or if requested
if (isset($_GET['clear_cache'])) {
    @array_map('unlink', glob(__DIR__.'/../bootstrap/cache/*.php'));
} elseif (file_exists($routeCache = __DIR__.'/../bootstrap/cache/routes-v7.php')) {
    $content = @file_get_contents($routeCache);
    if ($content && !str_contains($content, 'consultabeneficiariosinsumos')) {
        @unlink($routeCache);
    }
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
