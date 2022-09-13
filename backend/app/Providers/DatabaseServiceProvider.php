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
        $tenements = DB::connection('mysql_mis')->table('tenement')->where('id', 1)->get();
        foreach ($tenements as $key => $item) {
            $conn = array (
                'host' => $item->ip,
                'database' => $item->db_name,
                'username' => $item->db_user,
                'password' => $item->db_pwd
            );
            $final = array_merge($conn, $base);
            $new[$item->code] = $final;

            //抓斗数据库
            $obj_config_garbage_db = (new ConfigGarbageDB())->setConnection($item->code);
            $garbage_db_list = $obj_config_garbage_db->all();
            foreach ($garbage_db_list as $k9 => $db) {
                if($db && $db->type && $db->type=='mysql'){
                    $conn = array (
                        'host' => $db->ip,
                        'database' => $db->db_name,
                        'username' => $db->user,
                        'password' => $db->password,
                        'port' => $db->port
                    );
                    $final = array_merge($conn, $base);
                    $conn_name = 'garbage_' . $item->id . '_' . $db->id;
                    //$new[$conn_name] = $final;
                }
            }
        }

        $this->app['config']['database.connections'] = array_merge($this->app['config']['database.connections'], $new);
    }
}
