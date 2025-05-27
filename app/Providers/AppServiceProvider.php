<?php

namespace App\Providers;

use App\Models\Organization;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use Override;
use Spatie\LaravelIgnition\IgnitionServiceProvider;

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
            $this->app->singleton(fn(): Generator => Factory::create('es_ES'));
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
    #[Override]
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
