<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;
use Illuminate\Support\Facades\DB;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $user = DB::table('orgnization')->where('id', 1)->first();

        $new = [];
        $conn = array (
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'wmmis',
            'username' => 'root',
            'password' => '64y7nudx',
        );

        $base = array(
            'port' => '3306',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null
        );

        $final = array_merge($conn, $base);
        $new['mis'] = $final;
        $this->app['config']['database.connections'] = array_merge($this->app['config']['database.connections'], $new);
    }
}
