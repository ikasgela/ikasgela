<?php

namespace App\Providers;

use App\Models\Organization;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Dusk\DuskServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use Override;
use Spatie\LaravelIgnition\IgnitionServiceProvider;
use Symfony\Component\HttpFoundation\Response;

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
            $subdomain = subdominio();
            $organization = Cache::tags('organization_' . $subdomain)
                ->remember('organization', config('ikasgela.eloquent_cache_time'), fn() => Organization::where('slug', $subdomain)->first());
            View::share('current_organization', $organization);
        }

        Paginator::useBootstrap();

        $this->registerAuthThrottleLimiters();
    }

    /**
     * Registers the rate limiters for sensitive public forms.
     *
     * See spec/03-features/authentication.md — R-AUTH-15 a R-AUTH-18.
     *
     * @return void
     */
    protected function registerAuthThrottleLimiters()
    {
        $throttleError = function (Request $request, array $headers) {
            $seconds = $headers['Retry-After'] ?? 60;

            throw ValidationException::withMessages([
                'email' => [trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ])],
            ])->status(Response::HTTP_TOO_MANY_REQUESTS);
        };

        // Registro (POST /register): límite por IP
        RateLimiter::for('auth-register', fn (Request $request) => Limit::perMinute(
            config('ikasgela.auth_throttle.register_max_attempts'),
            config('ikasgela.auth_throttle.register_decay_minutes')
        )->by($request->ip())->response($throttleError));

        // Recuperación (POST /password/email y POST /password/reset): límite por email+IP
        RateLimiter::for('auth-password-reset', fn (Request $request) => Limit::perMinute(
            config('ikasgela.auth_throttle.password_reset_max_attempts'),
            config('ikasgela.auth_throttle.password_reset_decay_minutes')
        )->by(strtolower((string) $request->input('email', '')) . '|' . $request->ip())->response($throttleError));

        // GET /tinymce_url (público, sin auth): límite por IP
        RateLimiter::for('auth-tinymce-url', fn (Request $request) => Limit::perMinute(
            config('ikasgela.auth_throttle.tinymce_url_max_attempts'),
            config('ikasgela.auth_throttle.tinymce_url_decay_minutes')
        )->by($request->ip())->response(fn (Request $request, array $headers) => response(
            'Too Many Attempts.',
            Response::HTTP_TOO_MANY_REQUESTS
        )));
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
            $this->app->register(\Fruitcake\LaravelDebugbar\ServiceProvider::class);
            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(DuskServiceProvider::class);
            $this->app->register(IgnitionServiceProvider::class);
            $this->app->register(TinkerServiceProvider::class);
        }
    }
}
