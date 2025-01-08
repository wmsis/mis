<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\ConfigGarbageDB;
use App\Models\SIS\ConfigHistorianDB;

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
            //租户连接
            $conn = array (
                'host' => $item->ip,
                'database' => $item->db_name,
                'username' => $item->db_user,
                'password' => $item->db_pwd
            );
            $final = array_merge($conn, $base);
            $new[$item->code] = $final;

            //本地MongoDB连接
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

        //租户下面电厂数据库配置的连接，依赖租户本地连接
        $new2 = [];
        foreach ($tenements as $key => $item) {
            //抓斗数据库连接
            $obj_config_garbage_db = (new ConfigGarbageDB())->setConnection($item->code);
            $garbage_db_list = $obj_config_garbage_db->whereNull("deleted_at")->get();
            foreach ($garbage_db_list as $k9 => $db) {
                if($db && $db->type && $db->type=='mysql'){
                    $conn = array (
                        'host' => $db->ip,
                        'database' => $db->db_name,
                        'username' => $db->user,
                        'password' => $db->password,
                        'port' => $db->port,
                        'charset' => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix' => '',
                        'prefix_indexes' => true,
                        'strict' => true,
                        'engine' => null
                    );
                    $final = array_merge($base, $conn);
                    $conn_name = 'garbage_' . $item->id . '_' . $db->id;
                    $new2[$conn_name] = $final;
                }
            }

            //historian 5.5数据库 转存于MongoDB
            $obj_config_historian_db = (new ConfigHistorianDB())->setConnection($item->code);
            $historian_db_list = $obj_config_historian_db->whereNull("deleted_at")->get();
            foreach ($historian_db_list as $k9 => $db) {
                if($db && $db->version && $db->version < 7){
                    $final = array (
                        'host' => $db->ip,
                        'database' => $db->db_name,
                        //'username' => $db->user,
                        //'password' => $db->password,
                        'port' => $db->port,
                        'driver' => 'mongodb'
                    );
                    $conn_name = 'historian_' . $item->id . '_' . $db->id;
                    $new2[$conn_name] = $final;
                }
            }
        }

        $this->app['config']['database.connections'] = array_merge($this->app['config']['database.connections'], $new2);
    }
}
