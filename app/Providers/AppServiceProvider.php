<?php

namespace App\Providers;

use App\Models\Organization;
use Form;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;

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
        Form::component('campoPassword', 'components.form.password', ['name', 'label' => null, 'attributes' => []]);
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

        Paginator::useBootstrap();

        /* This line set the Cloudflare's IP as a trusted proxy */
        // REF: https://khalilst.medium.com/get-real-client-ip-behind-cloudflare-in-laravel-189cb89059ff
        Request::setTrustedProxies(['REMOTE_ADDR'], Request::HEADER_X_FORWARDED_FOR);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
