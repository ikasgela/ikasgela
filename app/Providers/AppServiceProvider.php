<?php

namespace App\Providers;

use App\Category;
use App\Curso;
use App\Observers\CategoryObserver;
use App\Observers\CursoObserver;
use App\Observers\OrganizationObserver;
use App\Observers\PeriodObserver;
use App\Observers\TareaObserver;
use App\Observers\UnidadObserver;
use App\Observers\UserObserver;
use App\Organization;
use App\Period;
use App\Tarea;
use App\Unidad;
use App\User;
use Form;
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
        \URL::forceScheme('https');

        Form::component('campoTexto', 'components.form.text', ['name', 'label' => null, 'value' => null, 'attributes' => []]);
        Form::component('campoTextoLabel', 'components.form.text_label', ['name', 'label' => null, 'value' => null, 'attributes' => []]);
        Form::component('campoCheck', 'components.form.check', ['name', 'label' => null, 'value' => null, 'attributes' => []]);
        Form::component('campoCheckLabel', 'components.form.check_label', ['name', 'label' => null, 'value' => null, 'attributes' => []]);
        Form::component('campoTextArea', 'components.form.text_area', ['name', 'label' => null, 'value' => null, 'attributes' => []]);

        if (config('app.env', 'local') !== 'production') {
            $this->app->singleton(\Faker\Generator::class, function () {
                return \Faker\Factory::create('es_ES');
            });
        }

        if (!\App::runningInConsole()) {
            $organization = Organization::where('slug', subdominio())->first();
            View::share('current_organization', $organization);
        }

        User::observe(UserObserver::class);
        Tarea::observe(TareaObserver::class);

        Organization::observe(OrganizationObserver::class);
        Period::observe(PeriodObserver::class);
        Category::observe(CategoryObserver::class);
        Curso::observe(CursoObserver::class);
        Unidad::observe(UnidadObserver::class);

        Paginator::useBootstrap();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*        if ($this->app->environment() !== 'production') {
                    $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
                }*/
    }
}
