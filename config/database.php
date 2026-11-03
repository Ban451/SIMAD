<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// --- TRIK MEMBUAT SYMLINK STORAGE DI WASMER ---
// Cek jika folder public/storage belum ada, buatkan symlink ke storage/app/public
$storagePath = __DIR__ . '/../storage/app/public';
$publicStoragePath = __DIR__ . '/storage';

if (!file_exists($publicStoragePath) && file_exists($storagePath)) {
    @symlink($storagePath, $publicStoragePath);
}
// ----------------------------------------------

$app->handleRequest(Request::capture());