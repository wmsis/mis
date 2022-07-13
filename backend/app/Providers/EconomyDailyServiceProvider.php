<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EconomyDailyService;

class EconomyDailyServiceProvider extends ServiceProvider {

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
        $this->app->bind('EconomyDailyService', function ($app) {
            return new EconomyDailyService();
        });
    }
}
