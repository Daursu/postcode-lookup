<?php

namespace Lodge\Postcode;

use Illuminate\Support\ServiceProvider;

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
        if (method_exists($this, 'package')) {
            $this->package('lodge/postcode');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('postcode', function () {
            return new Postcode();
        });
        
        \Config::package('lodge/postcode-lookup','postcode');
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
