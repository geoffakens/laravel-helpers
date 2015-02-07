<?php namespace Akens\LaravelHelpers;

use Illuminate\Support\ServiceProvider;
use Akens\LaravelHelpers\Hashing\CakeHasher;

class LaravelHelpersServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $this->package('akens/laravel-helpers');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->bindShared('hash', function () {
            return new CakeHasher;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array('hash');
    }

}
