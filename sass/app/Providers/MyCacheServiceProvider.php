<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MyCacheService;

class MyCacheServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->bind('MyCacheService', function ($app) {
            return new MyCacheService();
        });
    }
}
