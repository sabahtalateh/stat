<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PredisServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\Predis\Client::class, function () {

            return new \Predis\Client([
                'host'   => env('REDIS_HOST'),
                'password' => env('REDIS_PASSWORD'),
                'port'   => env('REDIS_PORT'),
                'database' => env('REDIS_DATABASE')
            ]);

        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
