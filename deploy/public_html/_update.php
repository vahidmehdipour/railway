<?php

use Illuminate\Contracts\Console\Kernel;

/*
 * One-time updater for shared hosting without SSH.
 * Runs pending migrations and clears caches. Does NOT seed.
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

ob_start();

$publicRoot = __DIR__;
$appRoot = '/home3/hwuxndqg/rail';

if (! is_file($appRoot.'/vendor/autoload.php') || ! is_file($appRoot.'/bootstrap/app.php')) {
    echo "ERROR: Laravel app root not found at {$appRoot}.\n";
    exit(1);
}

foreach ((glob($appRoot.'/bootstrap/cache/*.php') ?: []) as $file) {
    @unlink($file);
}

foreach ((glob($appRoot.'/storage/framework/views/*.php') ?: []) as $file) {
    @unlink($file);
}

require $appRoot.'/vendor/autoload.php';

$app = require_once $appRoot.'/bootstrap/app.php';

$config = $app->make('config');
$dbName = $config->get('database.connections.mysql.database');

echo "APP_ROOT: {$appRoot}\n";
echo "DB_NAME:  {$dbName}\n\n";

if ($dbName !== 'hwuxndqg_rail') {
    echo "ERROR: Expected database hwuxndqg_rail, got {$dbName}.\n";
    echo "Check {$appRoot}/.env before running the updater.\n";
    exit(1);
}

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

$commands = [
    'optimize:clear' => [],
    'migrate' => ['--force' => true, '--no-interaction' => true],
];

foreach ($commands as $command => $parameters) {
    echo "== {$command} ==\n";

    $status = $kernel->call($command, $parameters);
    echo $kernel->output();
    echo "status: {$status}\n\n";

    if ($status !== 0) {
        echo "UPDATE FAILED. Check storage/logs/laravel.log\n";
        exit(1);
    }
}

echo "UPDATE DONE.\n";

if (PHP_SAPI !== 'cli' && @unlink(__FILE__)) {
    echo "_update.php was removed automatically.\n";
} else {
    echo "IMPORTANT: Delete public_html/_update.php manually now.\n";
}
