<?php

namespace App\Providers;

use Form;
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
