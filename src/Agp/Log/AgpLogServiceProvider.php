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

        //TODO Depois de fazer o package da listagem, terminar a view de relatório de acessos da empresa.
        //$this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
    }

    public function register()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'Log');
    }
}
