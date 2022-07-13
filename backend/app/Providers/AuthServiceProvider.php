<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //定义gate Gates 总是接收用户实例作为第一个参数
        $permissions = \App\Permission::all();
        foreach ($permissions as $permission){
            if($permission->api_url) {
                $arr = [];
                if(strpos($permission->api_url, "\n") !== FALSE){
                    $arr = explode("\n", $permission->api_url);
                }
                else{
                    $arr = [$permission->api_url];
                }

                foreach($arr as $item){
                    //路径加权限ID连接，防止同一个路径多次定义
                    Gate::define($item.$permission->id, function ($user) use ($permission) {
                        return $user->hasPermission($permission);
                    });
                }
            }
        }
    }
}
