<?php

namespace App\Providers;

use App\Listeners\ActivarUsuario;
use App\Listeners\LoginSuccess;
use App\Listeners\LogoutSuccess;
use App\Listeners\UserImpersonated;
use App\Listeners\UserImpersonatedEnded;
use App\Listeners\ZipStreamedListener;
use App\Models\Actividad;
use App\Models\Category;
use App\Models\Curso;
use App\Models\Group;
use App\Models\Organization;
use App\Models\Period;
use App\Models\Qualification;
use App\Models\Skill;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\User;
use App\Observers\ActividadObserver;
use App\Observers\CategoryObserver;
use App\Observers\CursoObserver;
use App\Observers\GroupObserver;
use App\Observers\OrganizationObserver;
use App\Observers\PeriodObserver;
use App\Observers\QualificationObserver;
use App\Observers\SkillObserver;
use App\Observers\TareaObserver;
use App\Observers\UnidadObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;
use Override;
use STS\ZipStream\Events\ZipStreamed;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Verified::class => [
            ActivarUsuario::class,
        ],
        Login::class => [
            LoginSuccess::class,
        ],
        Logout::class => [
            LogoutSuccess::class,
        ],
        TakeImpersonation::class => [
            UserImpersonated::class,
        ],
        LeaveImpersonation::class => [
            UserImpersonatedEnded::class,
        ],
        ZipStreamed::class => [
            ZipStreamedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    #[Override]
    public function boot()
    {
        parent::boot();

        Organization::observe(OrganizationObserver::class);
        Period::observe(PeriodObserver::class);
        Category::observe(CategoryObserver::class);
        Curso::observe(CursoObserver::class);
        Unidad::observe(UnidadObserver::class);
        Actividad::observe(ActividadObserver::class);

        Group::observe(GroupObserver::class);
        User::observe(UserObserver::class);

        Tarea::observe(TareaObserver::class);

        Qualification::observe(QualificationObserver::class);
        Skill::observe(SkillObserver::class);
    }
}
