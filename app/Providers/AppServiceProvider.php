<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. HAPUS CACHE CONFIG SECARA PAKSA (Laravel 11)
        $configCachePath = base_path('bootstrap/cache/config.php');
        if (File::exists($configCachePath)) {
            File::delete($configCachePath);
        }

        // 2. PAKSA LARAVEL MENGGUNAKAN DATABASE WASMER
        // Ini akan menimpa setelan apapun dari .env atau config/database.php
        Config::set('database.connections.mysql.host', 'db.fr-roub1.bengt.wasmernet.com');
        Config::set('database.connections.mysql.port', '20184');
        Config::set('database.connections.mysql.database', 'db_41046cc6');
        Config::set('database.connections.mysql.username', 'user_31294914');
        
        // --- GANTI PASSWORD DI BAWAH INI JIKA KAMU MELAKUKAN ROTATE CREDENTIALS ---
        Config::set('database.connections.mysql.password', 'pw_3H4Q3FxZg4l9Rw5rLg0M6zI4BKMYqoHk');
        // --------------------------------------------------------------------------

        // 3. Putus koneksi lama agar Laravel memaksa membuat koneksi baru dengan config di atas
        DB::purge('mysql');
    }
}