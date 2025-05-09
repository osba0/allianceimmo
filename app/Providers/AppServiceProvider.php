<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\WebsiteComposer;
use Illuminate\Pagination\Paginator;
use App\Models\Proprietaires;
use App\Observers\ProprietaireObserver;
use App\Repositories\Template\TemplateRepositoryInterface;
use App\Repositories\Template\TemplateRepository;

use App\Services\Core\Repositories\Operation\OperationRepository;
use App\Services\Core\Repositories\Operation\OperationRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
     /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
            TemplateRepositoryInterface::class => TemplateRepository::class,
            OperationRepositoryInterface::class => OperationRepository::class,
        ];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

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
