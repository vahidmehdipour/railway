<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

/*
 * Temporary diagnostic script for the hosted install.
 * Delete it immediately after reading the output.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$token = 'a703d767de202e561258250e07233d0f';

if (($_GET['token'] ?? '') !== $token) {
    http_response_code(404);
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

ob_start();

$publicRoot = __DIR__;
$appRoot = '/home3/hwuxndqg/rail';

echo 'PHP version: '.PHP_VERSION."\n";
echo "Public root: {$publicRoot}\n";
echo "App root (fixed): {$appRoot}\n\n";

if (! is_file($appRoot.'/vendor/autoload.php') || ! is_file($appRoot.'/bootstrap/app.php')) {
    echo "ERROR: Laravel app root not found at {$appRoot}.\n";
    echo "Check the cPanel repository path.\n";
    exit(1);
}

$checks = [
    'vendor/autoload.php' => is_file($appRoot.'/vendor/autoload.php'),
    'bootstrap/app.php' => is_file($appRoot.'/bootstrap/app.php'),
    '.env' => is_file($appRoot.'/.env'),
    'storage writable' => is_writable($appRoot.'/storage'),
    'bootstrap/cache writable' => is_writable($appRoot.'/bootstrap/cache'),
];

foreach ($checks as $label => $ok) {
    echo str_pad($label, 28).': '.($ok ? 'OK' : 'FAIL')."\n";
}

echo "\n";

require $appRoot.'/vendor/autoload.php';

$app = require_once $appRoot.'/bootstrap/app.php';

// Mirror the custom front controller so the checks below match real requests.
$app->usePublicPath($publicRoot);

$app->make(Kernel::class)->bootstrap();

$config = $app->make('config');

echo 'APP_ENV:   '.$config->get('app.env')."\n";
echo 'APP_DEBUG: '.($config->get('app.debug') ? 'true' : 'false')."\n";
echo 'APP_KEY:   '.($config->get('app.key') ? 'set' : 'MISSING')."\n";
echo 'DB_CONN:   '.$config->get('database.default')."\n";
echo 'DB_HOST:   '.$config->get('database.connections.mysql.host')."\n";
echo 'DB_NAME:   '.$config->get('database.connections.mysql.database')."\n";
echo 'DB_USER:   '.$config->get('database.connections.mysql.username')."\n";
echo 'Public path: '.$app->publicPath()."\n";
echo 'Vite manifest: '.(is_file($app->publicPath('build/manifest.json')) ? 'OK' : 'MISSING')."\n\n";

try {
    DB::connection()->select('select 1 as ok');
    echo "DB query: OK\n";
} catch (Throwable $e) {
    echo "DB query: FAIL\n";
    echo get_class($e).': '.$e->getMessage()."\n";
}

echo "\n== Encrypter check ==\n";

try {
    $app->make('encrypter');
    echo "Encrypter: OK\n";
} catch (Throwable $e) {
    echo "Encrypter: FAIL\n";
    echo get_class($e).': '.$e->getMessage()."\n";
    echo 'APP_CIPHER: '.$config->get('app.cipher')."\n";

    $rawKey = (string) $config->get('app.key');
    echo 'APP_KEY prefix: '.substr($rawKey, 0, 7).' | strlen: '.strlen($rawKey)."\n";
}

echo "\n== Last 40 log lines ==\n";

$log = $appRoot.'/storage/logs/laravel.log';

if (is_file($log)) {
    $lines = file($log, FILE_IGNORE_NEW_LINES);
    echo implode("\n", array_slice($lines, -40))."\n";
} else {
    echo "No log file found.\n";
}

echo "\n== index.php check ==\n";

$indexFile = $publicRoot.'/index.php';

if (is_file($indexFile)) {
    $content = file_get_contents($indexFile);
    echo str_contains($content, 'usePublicPath')
        ? "index.php looks like the custom deploy version.\n"
        : "index.php looks OLD/default. Re-copy deploy/public_html/index.php.\n";
} else {
    echo "index.php missing.\n";
}

if (PHP_SAPI !== 'cli' && @unlink(__FILE__)) {
    echo "\n_diag.php was removed automatically.\n";
} else {
    echo "\nDelete public_html/_diag.php manually.\n";
}
