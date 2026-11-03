namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // PAKSA LARAVEL MENGGUNAKAN DATABASE WASMER
        Config::set('database.connections.mysql.host', 'db.fr-roub1.bengt.wasmernet.com');
        Config::set('database.connections.mysql.port', '20184');
        Config::set('database.connections.mysql.database', 'db_41046cc6');
        Config::set('database.connections.mysql.username', 'user_31294914');
        Config::set('database.connections.mysql.password', 'pw_3H4Q3FxZg4l9Rw5rLg0M6zI4BKMYqoHk'); // Pastikan password ini masih valid
        
        // Putus koneksi lama jika ada
        DB::purge('mysql');
    }
}