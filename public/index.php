<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// --- TAMBAHKAN KODE MIGRASI OTOMATIS INI ---
try {
    // Cek apakah tabel 'users' sudah ada. Kalau belum, jalankan migrasi.
    $pdo = new PDO(
        'mysql:host=db.fr-roub1.bengt.wasmernet.com;port=20184;dbname=db_41046cc6',
        'user_cefe2475',
        'pw_3H4Q3FxZg4l9Rw5rLg0M6zI4BKMYqoHk'
    );
    $stmt = $pdo->query("SELECT 1 FROM users LIMIT 1");
} catch (Exception $e) {
    // Jika gagal (tabel belum ada), jalankan perintah migrasi via PHP
    if (strpos($e->getMessage(), '1146') !== false || strpos($e->getMessage(), 'Base table or view not found') !== false) {
        // Kita jalankan migrasi di latar belakang (tanpa mengganggu loading)
        exec('php artisan migrate:fresh --seed --force > /dev/null 2>&1 &');
    }
}
// -------------------------------------------

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());