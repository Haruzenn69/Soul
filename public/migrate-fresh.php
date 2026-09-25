<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$enabled = filter_var($app['config']->get('app.migrate_fresh_enabled'), FILTER_VALIDATE_BOOL);
$configuredToken = (string) $app['config']->get('app.migrate_fresh_token');
$providedToken = (string) ($_GET['token'] ?? '');

if (! $enabled || $configuredToken === '' || $providedToken === '' ||
    ! hash_equals($configuredToken, $providedToken)) {
    http_response_code(404);
    exit('Not Found');
}

try {
    $exitCode = $kernel->call('migrate:fresh', [
        '--seed' => true,
        '--force' => true,
    ]);

    header('Content-Type: text/plain; charset=utf-8');
    http_response_code($exitCode === 0 ? 200 : 500);
    echo $kernel->output();
    echo PHP_EOL.'Exit code: '.$exitCode;
} catch (Throwable $exception) {
    report($exception);

    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Migration failed. Check storage/logs/laravel.log for details.';
}
