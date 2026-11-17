<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // 1. Hapus cache config jika ada (Paksa baca ulang)
        $configCache = base_path('bootstrap/cache/config.php');
        if (File::exists($configCache)) {
            File::delete($configCache);
        }

        // 2. Paksa koneksi ke database Wasmer
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => 'db.fr-roub1.bengt.wasmernet.com',
            'database.connections.mysql.port' => '20184',
            'database.connections.mysql.database' => 'db_41046cc6',
            'database.connections.mysql.username' => 'user_31294914',
            'database.connections.mysql.password' => 'pw_3H4Q3FxZg4l9Rw5rLg0M6zI4BKMYqoHk',
        ]);

        // 3. Putus koneksi lama agar memakai setelan baru
        DB::purge('mysql');
    }
}