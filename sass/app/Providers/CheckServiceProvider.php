<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CheckService;

class CheckServiceProvider extends ServiceProvider {

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
        $this->app->bind('CheckService', function ($app) {
            return new CheckService();
        });
    }
}
