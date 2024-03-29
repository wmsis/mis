<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\ConfigGarbageDB;

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
     * 合并各租户数据库连接信息
     *
     * @return void
     */
    public function boot()
    {
        $new = [];
        $base = array(
            'driver' => 'mysql',
            'port' => '3306',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null
        );

        //租户数据库
        $tenements = DB::connection('mysql_mis')->table('tenement')->get();
        foreach ($tenements as $key => $item) {
            //本地mysql
            $conn = array (
                'host' => $item->ip,
                'database' => $item->db_name,
                'username' => $item->db_user,
                'password' => $item->db_pwd
            );
            $final = array_merge($conn, $base);
            $new[$item->code] = $final;

            //本地MongoDB
            $final_mongo = array (
                'host' => $item->ip,
                'database' => $item->db_name,
                //'username' => $item->user,
                //'password' => $item->password,
                'port' => 27017,
                'driver' => 'mongodb'
            );
            $conn_name_mongo = $item->code . '_mongo';
            $new[$conn_name_mongo] = $final_mongo;
        }

        $this->app['config']['database.connections'] = array_merge($this->app['config']['database.connections'], $new);
    }
}
