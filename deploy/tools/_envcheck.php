<?php

use Dotenv\Dotenv;

/*
 * Temporary .env validator for shared hosting without SSH.
 * It prints the exact Dotenv parse error before Laravel boots.
 * Delete it immediately after use.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$token = 'a703d767de202e561258250e07233d0f';

if (($_GET['token'] ?? '') !== $token) {
    http_response_code(404);
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

$appRoot = '/home3/hwuxndqg/rail';
$envFile = $appRoot.'/.env';

require $appRoot.'/vendor/autoload.php';

echo "== ENV CHECK ==\n";
echo "App root: {$appRoot}\n";
echo '.env: '.(is_file($envFile) ? filesize($envFile).' bytes' : 'MISSING')."\n\n";

if (! is_file($envFile)) {
    echo "ERROR: .env not found. Create {$envFile}\n";
    exit(1);
}

$raw = file_get_contents($envFile);
$secretKeys = ['DB_PASSWORD', 'APP_KEY', 'MAIL_PASSWORD', 'AWS_SECRET_ACCESS_KEY'];

foreach (explode("\n", $raw) as $i => $line) {
    $trim = trim($line);

    if ($trim === '' || str_starts_with($trim, '#')) {
        continue;
    }

    if (preg_match('/^([A-Z0-9_]+)\s*=/', $trim, $m) === 1) {
        $key = $m[1];
        $value = preg_match('/=\s*(.*)$/s', $trim, $v) === 1 ? trim($v[1]) : '';

        if (in_array($key, $secretKeys, true)) {
            $value = '***';
        }

        echo sprintf("%3d  %-28s = %s\n", $i + 1, $key, $value);
    } else {
        echo sprintf("%3d  INVALID LINE: %s\n", $i + 1, $trim);
    }
}

echo "\n== Dotenv parse ==\n";

try {
    $dotenv = Dotenv::createImmutable($appRoot, '.env');
    $dotenv->load();
    echo "Dotenv parse: OK\n";
} catch (Throwable $e) {
    echo 'FAIL: '.get_class($e)."\n";
    echo $e->getMessage()."\n";
    exit(1);
}

echo "\nENV CHECK DONE. Delete this file now.\n";

if (PHP_SAPI !== 'cli' && @unlink(__FILE__)) {
    echo "_envcheck.php was removed automatically.\n";
} else {
    echo "Delete public_html/_envcheck.php manually.\n";
}
