<?php

// 1. Buat folder sementara (/tmp) agar sistem Serverless Vercel bisa menulis file
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Paksa Laravel membuang Cache lama dan membuat Cache baru di folder /tmp
$_SERVER['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
$_SERVER['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
$_SERVER['APP_CONFIG_CACHE']   = '/tmp/bootstrap/cache/config.php';
$_SERVER['APP_ROUTES_CACHE']   = '/tmp/bootstrap/cache/routes.php';
$_SERVER['APP_EVENTS_CACHE']   = '/tmp/bootstrap/cache/events.php';

// 3. Load Autoloader bawaan Composer
require __DIR__ . '/../vendor/autoload.php';

// 4. Panggil aplikasi Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// 5. Belokkan folder Storage Laravel ke folder /tmp
$app->useStoragePath('/tmp/storage');

// 6. Jalankan Aplikasi (Mendukung Laravel 10 maupun 11)
$request = Illuminate\Http\Request::capture();

if (method_exists($app, 'handleRequest')) {
    // Untuk Laravel 11
    $app->handleRequest($request);
} else {
    // Untuk Laravel 10
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
}