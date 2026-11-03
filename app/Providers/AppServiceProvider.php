<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

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
        // Paksa matikan setelan dari .env / Wasmer env, dan kunci ke database Wasmer
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => 'db.fr-roub1.bengt.wasmernet.com',
            'database.connections.mysql.port' => '20184',
            'database.connections.mysql.database' => 'db_41046cc6',
            'database.connections.mysql.username' => 'user_31294914',
            'database.connections.mysql.password' => 'pw_3H4Q3FxZg4l9Rw5rLg0M6zI4BKMYqoHk',
        ]);

        // Paksa hapus koneksi yang mungkin sudah terbentuk dengan settingan 127.0.0.1
        DB::purge('mysql');
    }
}