<?php

use Illuminate\Contracts\Console\Kernel;

/*
 * One-time installer for shared hosting without SSH.
 * Delete this file immediately after a successful run.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

@set_time_limit(0);
ignore_user_abort(true);

$token = 'a703d767de202e561258250e07233d0f';

if (($_GET['token'] ?? '') !== $token) {
    http_response_code(404);
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

/*
 * Locate the Laravel project root.
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
    echo "ERROR: Laravel app root not found.\n";
    echo "Upload the project folder (vendor + bootstrap) beside public_html.\n";
    exit(1);
}

// Remove any cached config/routes/views generated on the local machine.
foreach ((glob($appRoot.'/bootstrap/cache/*.php') ?: []) as $file) {
    @unlink($file);
}

foreach ((glob($appRoot.'/storage/framework/views/*.php') ?: []) as $file) {
    @unlink($file);
}

require $appRoot.'/vendor/autoload.php';

$app = require_once $appRoot.'/bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

$commands = [
    'migrate' => ['--force' => true, '--no-interaction' => true],
    'db:seed' => ['--force' => true, '--no-interaction' => true, '--class' => 'DatabaseSeeder'],
];

foreach ($commands as $command => $parameters) {
    echo "== {$command} ==\n";

    $status = $kernel->call($command, $parameters);
    echo $kernel->output();
    echo "status: {$status}\n\n";

    if ($status !== 0) {
        echo "INSTALL FAILED. Check storage/logs/laravel.log\n";
        exit(1);
    }
}

echo "DONE. The application is ready.\n";

if (PHP_SAPI !== 'cli' && @unlink(__FILE__)) {
    echo "_setup.php was removed automatically.\n";
} else {
    echo "IMPORTANT: Delete public_html/_setup.php manually now.\n";
}
