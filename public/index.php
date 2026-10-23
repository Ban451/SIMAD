<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// =========================================================================
// TRIK PAMUNGKAS: BUAT TABEL OTOMATIS VIA SQL MANUAL (TANPA ARTISAN)
// =========================================================================
try {
    $pdo = new PDO(
        'mysql:host=db.fr-roub1.bengt.wasmernet.com;port=20184;dbname=db_41046cc6',
        'user_31294914', 
        'pw_3H4Q3FxZg4l9Rw5rLg0M6zI4BKMYqoHk'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cek apakah tabel 'users' sudah ada
    $stmt = $pdo->query("SELECT 1 FROM users LIMIT 1");
} catch (Exception $e) {
    // Jika tabel tidak ada (error 1146), kita buat tabel manual pakai query SQL
    if (strpos($e->getMessage(), '1146') !== false || strpos($e->getMessage(), 'Base table') !== false) {
        try {
            // 1. Buat tabel kelas
            $pdo->exec("CREATE TABLE IF NOT EXISTS kelas (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nama_kelas VARCHAR(255) NOT NULL,
                tingkat VARCHAR(255) NOT NULL,
                jurusan VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )");

            // 2. Buat tabel users
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nama VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NULL,
                nis VARCHAR(255) UNIQUE NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'guru', 'siswa') DEFAULT 'siswa',
                kelas_id BIGINT UNSIGNED NULL,
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE SET NULL
            )");

            // 3. Buat tabel kategori_dokumen
            $pdo->exec("CREATE TABLE IF NOT EXISTS kategori_dokumen (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nama_kategori VARCHAR(255) NOT NULL,
                kode VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )");

            // 4. Buat tabel dokumen
            $pdo->exec("CREATE TABLE IF NOT EXISTS dokumen (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                judul VARCHAR(255) NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                nama_file VARCHAR(255) NOT NULL,
                keterangan TEXT NULL,
                status ENUM('pending', 'aktif', 'arsip') DEFAULT 'pending',
                siswa_id BIGINT UNSIGNED NOT NULL,
                kategori_id BIGINT UNSIGNED NOT NULL,
                uploaded_by BIGINT UNSIGNED NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (siswa_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (kategori_id) REFERENCES kategori_dokumen(id) ON DELETE CASCADE,
                FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
            )");

            // 5. Buat tabel dokumen_requests
            $pdo->exec("CREATE TABLE IF NOT EXISTS dokumen_requests (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                siswa_id BIGINT UNSIGNED NOT NULL,
                kategori_id BIGINT UNSIGNED NOT NULL,
                judul VARCHAR(255) NOT NULL,
                keterangan TEXT NULL,
                status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                alasan TEXT NULL,
                processed_at TIMESTAMP NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (siswa_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (kategori_id) REFERENCES kategori_dokumen(id) ON DELETE CASCADE
            )");

            // Insert data admin default agar bisa login
            $password = password_hash('password', PASSWORD_BCRYPT);
            $pdo->exec("INSERT INTO users (nama, email, password, role, created_at, updated_at) 
                        VALUES ('Admin SIMAD', 'admin@simad.com', '$password', 'admin', NOW(), NOW())");

        } catch (Exception $ex) {
            die("Gagal membuat tabel: " . $ex->getMessage());
        }
    }
}
// =========================================================================

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