<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceBindingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Service bindings
        $this->app->bind(\App\Contracts\PetServiceInterface::class, \App\Services\PetService::class);
        $this->app->bind(\App\Contracts\BattleServiceInterface::class, \App\Services\BattleService::class);
        
        // Future bindings:
        // $this->app->bind(TimeServiceInterface::class, TimeService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
