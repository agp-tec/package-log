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
    }

    public function register()
    {

    }
}
