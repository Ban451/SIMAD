<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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

// --- TRIK PINTU BELAKANG UNTUK MIGRASI ---
try {
    // Coba cek apakah tabel users ada
    $pdo = new PDO(
        'mysql:host=db.fr-roub1.bengt.wasmernet.com;port=20184;dbname=db_41046cc6',
        'user_31294914', 
        'pw_3H4Q3FxZg4l9Rw5rLg0M6zI4BKMYqoHk'
    );
    $pdo->query("SELECT 1 FROM users LIMIT 1");
} catch (Exception $e) {
    // Jika tabel tidak ada, jalankan migrasi menggunakan Artisan Facade Laravel
    if (strpos($e->getMessage(), '1146') !== false || strpos($e->getMessage(), 'Base table') !== false) {
        // Coba panggil artisan lewat PHP
        try {
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        } catch (\Exception $ex) {
            // Jika artisan gagal, biarkan (kita akan coba cara lain)
        }
    }
}
// -------------------------------------------

$app->handleRequest(Request::capture());