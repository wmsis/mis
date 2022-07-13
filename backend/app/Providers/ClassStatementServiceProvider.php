<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ClassStatementService;

class ClassStatementServiceProvider extends ServiceProvider {

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
        $this->app->bind('ClassStatementService', function ($app) {
            return new ClassStatementService();
        });
    }
}
