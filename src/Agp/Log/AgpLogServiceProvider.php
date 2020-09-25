<?php

namespace Agp\Log;

use Illuminate\Support\ServiceProvider;

class AgpLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/log.php' => config_path('log.php'),
        ], 'config');

//        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
    }

    public function register()
    {
//        $this->loadViewsFrom(__DIR__ . '/Views', 'Log');
    }
}
