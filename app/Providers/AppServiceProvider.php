<?php

namespace App\Providers;

use URL;
use Faker\Generator;
use Faker\Factory;
use App;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Spatie\LaravelIgnition\IgnitionServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use App\Models\Organization;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceScheme('https');

        if (config('app.env', 'local') !== 'production') {
            $this->app->singleton(function (): Generator {
                return Factory::create('es_ES');
            });
        }

        if (!App::runningInConsole()) {
            $organization = Organization::where('slug', subdominio())->first();
            View::share('current_organization', $organization);
        }

        Paginator::useBootstrap();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->environment('production')) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(DuskServiceProvider::class);
            $this->app->register(IgnitionServiceProvider::class);
            $this->app->register(TinkerServiceProvider::class);
        }
    }
}
