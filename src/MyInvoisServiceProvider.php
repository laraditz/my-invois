<?php

namespace Laraditz\MyInvois;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MyInvoisServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'my-invois');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'my-invois');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('myinvois.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/my-invois'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/my-invois'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/my-invois'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'myinvois');

        // Register the main class to use with the facade
        $this->app->singleton('myinvois', function () {
            return new MyInvois(
                client_id: config('myinvois.client_id'),
                client_secret: config('myinvois.client_secret'),
                is_sandbox: config('myinvois.sandbox.mode'),
                certificate_path: config('myinvois.certificate_path'),
                private_key_path: config('myinvois.private_key_path'),
                passphrase: config('myinvois.passphrase'),
                disk: config('myinvois.disk'),
                document_path: config('myinvois.document_path'),
            );
        });

        $this->app->singleton('myinvoishelper', function () {
            return new MyInvoisHelper();
        });
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            Route::name('tiktok.')->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('myionvois.routes.prefix'),
            'middleware' => config('myionvois.middleware'),
        ];
    }
}
