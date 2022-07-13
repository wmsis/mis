<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\JPushService;

class JPushServiceProvider extends ServiceProvider {

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
        $this->app->bind('JPushService', function ($app) {
            return new JPushService();
        });
    }
}