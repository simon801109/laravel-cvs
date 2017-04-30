<?php

namespace Simon801109\Cvs;

use Illuminate\Support\ServiceProvider;

class CvsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/cvs.php' => config_path('cvs.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/cvs.php', 'cvs'
        );
    }
}
