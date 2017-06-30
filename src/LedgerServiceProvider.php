<?php

namespace FannyPack\Ledger;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class LedgerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {            
            $this->publishes([
                __DIR__.'/../resources/assets/js' => base_path('resources/assets/js'),
            ], 'ledger-components');

            $this->publishes([
                __DIR__.'/../resources/assets/css' => base_path('resources/assets/css'),
            ], 'ledger-components');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Ledger::class, function($app){
            return new Ledger($app['router']);
        });

        $this->app->when(EntryRepository::class)
            ->needs(Carbon::class)
            ->give(function(){
                return new Carbon($tz="EAT");
            });
    }

    /**
     * services this provider provides
     * 
     * @return array
     */
    public function provides()
    {
        return [Ledger::class];
    }
}
