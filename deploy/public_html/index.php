<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
 * Locate the Laravel project root. On shared hosting the application files are
 * usually one level above public_html, but the folder name can vary.
 */
$publicRoot = __DIR__;
$parent = dirname($publicRoot);
$grandparent = dirname($parent);
$roots = [$publicRoot, $parent, $grandparent];

foreach ([$parent, $grandparent] as $scanDir) {
    foreach ((glob($scanDir.'/*', GLOB_ONLYDIR) ?: []) as $dir) {
        $roots[] = $dir;
    }
}

$appRoot = null;

foreach ($roots as $root) {
    if (is_file($root.'/vendor/autoload.php') && is_file($root.'/bootstrap/app.php')) {
        $appRoot = $root;
        break;
    }
}

if (! $appRoot) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Laravel app root not found. Make sure the project folder (with vendor and bootstrap) is uploaded beside public_html.';
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
