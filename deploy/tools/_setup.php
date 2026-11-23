<?php

use Illuminate\Contracts\Console\Kernel;

/*
 * One-time installer for shared hosting without SSH.
 * Refuses to run against an already-installed database so db:seed can
 * never restore default profiles/stations over the user's own data.
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

/*
 * Shared-hosting deployment: the Laravel project lives in a fixed folder.
 * Keep this path in sync with the cPanel Git repository path.
 */
$publicRoot = __DIR__;
$appRoot = '/home3/hwuxndqg/rail';

if (! is_file($appRoot.'/vendor/autoload.php') || ! is_file($appRoot.'/bootstrap/app.php')) {
    echo "ERROR: Laravel app root not found at {$appRoot}.\n";
    echo "Check the cPanel repository path.\n";
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
$kernel->bootstrap();

$config = $app->make('config');
$dbName = $config->get('database.connections.mysql.database');

echo "APP_ROOT: {$appRoot}\n";
echo "DB_NAME:  {$dbName}\n\n";

if ($dbName !== 'hwuxndqg_rail') {
    echo "ERROR: Expected database hwuxndqg_rail, got {$dbName}.\n";
    echo "Check {$appRoot}/.env before running the installer.\n";
    exit(1);
}

/*
 * Safety guard: db:seed restores default profiles and stations. It must run
 * only once, on an empty database. If any migration or user already exists,
 * stop here so existing data is never overwritten.
 */
$connection = $app->make('db')->connection();
$schema = $connection->getSchemaBuilder();

$alreadyInstalled = false;

if ($schema->hasTable('migrations')) {
    $alreadyInstalled = $connection->table('migrations')->count() > 0;
}

if (! $alreadyInstalled && $schema->hasTable('users')) {
    $alreadyInstalled = $connection->table('users')->count() > 0;
}

if ($alreadyInstalled) {
    echo "ERROR: Database {$dbName} already contains installed data.\n";
    echo "The installer was NOT run, so nothing was seeded or changed.\n\n";
    echo "For future updates use only:\n";
    echo "https://rail.lemooshop.ir/_update.php?token={$token}\n";
    exit(1);
}

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
