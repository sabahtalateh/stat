<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class StatisticStorageProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Components\Statistic\Storage::class, function () {
            /** @var \Predis\Client $predis */
            $predis = app(\Predis\Client::class);
            return new \App\Components\Statistic\RedisStorage($predis);
        });
    }
}
