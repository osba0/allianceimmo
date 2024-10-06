<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\WebsiteComposer;
use Illuminate\Pagination\Paginator;
use App\Models\Proprietaires;
use App\Observers\ProprietaireObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Use bootstrap on the Pagination
        Paginator::useBootstrap();

        Proprietaires::observe(ProprietaireObserver::class);
    }
}
