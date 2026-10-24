<?php

use App\Models\Task;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Laravel\Mcp\Facades\Mcp;
use Laravel\Mcp\Server\AppResource;
use Laravel\Mcp\Server\Registrar;
use OrbitBrowser\OrbitBrowserServer;

define('LARAVEL_START', microtime(true));

try {
    $application = $argv[1];
    $database = realpath(getenv('ORBIT_DATABASE_PATH') ?: getenv('HOME').'/Library/Application Support/orbit/database/database.sqlite');
    if ($database === false || ! is_file($database) || ! is_readable($database)) {
        throw new RuntimeException('Open Orbit and create a workspace first. Set ORBIT_DATABASE_PATH to use another existing Orbit SQLite database.');
    }

    $storage = getenv('HOME').'/Library/Caches/orbit-browser';
    foreach (['bootstrap', 'framework/views', 'logs'] as $directory) {
        $path = $storage.'/'.$directory;
        if (! is_dir($path) && ! mkdir($path, 0700, true) && ! is_dir($path)) {
            throw new RuntimeException('Could not create the Orbit Browser cache directory.');
        }
    }

    $environment = [
        'NATIVEPHP_RUNNING' => 'false',
        'LARAVEL_STORAGE_PATH' => $storage,
        'VIEW_COMPILED_PATH' => $storage.'/framework/views',
        'APP_CONFIG_CACHE' => $storage.'/bootstrap/config.php',
        'APP_SERVICES_CACHE' => $storage.'/bootstrap/services.php',
        'APP_PACKAGES_CACHE' => $storage.'/bootstrap/packages.php',
        'APP_ROUTES_CACHE' => $storage.'/bootstrap/routes.php',
        'APP_EVENTS_CACHE' => $storage.'/bootstrap/events.php',
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => $database,
        'DB_URL' => '',
        'CACHE_STORE' => 'array',
        'LOG_CHANNEL' => 'stderr',
        'APP_DEBUG' => 'false',
    ];
    foreach ($environment as $name => $value) {
        putenv($name.'='.$value);
        $_ENV[$name] = $_SERVER[$name] = $value;
    }

    require $application.'/vendor/autoload.php';
    if (! class_exists(AppResource::class) || ! method_exists(Task::class, 'renderDescription')) {
        throw new RuntimeException('Update Orbit to a version with MCP Apps support (tested with Orbit 0.5.1).');
    }
    spl_autoload_register(function (string $class): void {
        if (str_starts_with($class, 'OrbitBrowser\\')) {
            $file = dirname(__DIR__).'/src/'.substr($class, strlen('OrbitBrowser\\')).'.php';
            if (is_file($file)) {
                require $file;
            }
        }
    });

    $app = require $application.'/bootstrap/app.php';
    $app->addAbsoluteCachePathPrefix($storage);
    $app->make(Kernel::class)->bootstrap();

    // Never change the workspace journal mode, and keep every reconnect read-only.
    config([
        'database.default' => 'sqlite',
        'database.connections.sqlite' => [
            'driver' => 'sqlite',
            'database' => 'file:'.str_replace('%2F', '/', rawurlencode($database)).'?mode=ro',
            'prefix' => '',
            'busy_timeout' => 5000,
            'pragmas' => ['query_only' => 1],
        ],
    ]);
    DB::purge('sqlite');
    DB::connection()->getPdo();

    View::addNamespace('orbit-browser', dirname(__DIR__).'/views');
    Mcp::local('orbit-browser', OrbitBrowserServer::class);
    $app->make(Registrar::class)->getLocalServer('orbit-browser')();
} catch (Throwable $exception) {
    fwrite(STDERR, 'Orbit Browser: '.$exception->getMessage()."\n");
    exit(1);
}
