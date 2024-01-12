<?php

namespace Rodion15\PayokIo;

use Illuminate\Support\ServiceProvider;

class PayokIoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/payokio.php' => config_path('payokio.php'),
        ], 'config');

        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/payokio.php', 'payokio');

        $this->app->singleton('payokio', function () {
            return $this->app->make(PayokIo::class);
        });

        $this->app->alias('payokio', 'PayokIo');

        //
    }
}
