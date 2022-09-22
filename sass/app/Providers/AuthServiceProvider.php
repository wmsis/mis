<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\SIS\API;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
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
        $lists = API::all();
        foreach ($lists as $api){
            if($api->url) {
                $arr = [];
                if(strpos($api->url, "\n") !== FALSE){
                    $arr = explode("\n", $api->url);
                }
                else{
                    $arr = [$api->url];
                }

                foreach($arr as $item){
                    //路径加权限ID连接，防止同一个路径多次定义
                    Gate::define($item.$api->id, function ($user) use ($api) {
                        return $user->hasApi($api);
                    });
                }
            }
        }
    }
}
