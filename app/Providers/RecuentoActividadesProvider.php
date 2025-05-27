<?php

namespace App\Providers;

use App\Http\View\Composers\ActividadesAsignadasComposer;
use App\Http\View\Composers\ActividadesPendientesComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Override;

class RecuentoActividadesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    #[Override]
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', ActividadesAsignadasComposer::class);
        View::composer('*', ActividadesPendientesComposer::class);
    }
}
