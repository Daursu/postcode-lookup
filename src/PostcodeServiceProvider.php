<?php

namespace Lodge\Postcode;

use Illuminate\Support\ServiceProvider;
use Lodge\Postcode\Gateways\GoogleApi;

class PostcodeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/postcode.php' => config_path('postcode.php'),
            ], 'postcode-config');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/postcode.php', 'postcode'
        );

        $this->app->singleton('postcode', function () {
            return new Postcode(
                new GoogleApi(config('postcode.google_api_key'))
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['postcode'];
    }
}
