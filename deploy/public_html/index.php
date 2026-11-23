<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
 * Shared-hosting deployment: the Laravel project lives in a fixed folder.
 * Keep this path in sync with the cPanel Git repository path.
 */
$publicRoot = __DIR__;
$appRoot = '/home3/hwuxndqg/rail';

if (! is_file($appRoot.'/vendor/autoload.php') || ! is_file($appRoot.'/bootstrap/app.php')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Laravel app root not found at '.$appRoot.'. Check the cPanel repository path.';
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $appRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $appRoot.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appRoot.'/bootstrap/app.php';

/*
 * The built Vite assets live in this folder (the Document Root). Pointing the
 * application's public path here lets @vite() find build/manifest.json without
 * duplicating the build inside the application folder.
 */
$app->usePublicPath($publicRoot);

$app->handleRequest(Request::capture());
