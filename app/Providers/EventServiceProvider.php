<?php

namespace App\Providers;

use App\Models\Actividad;
use App\Models\Category;
use App\Models\Curso;
use App\Models\Group;
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
use App\Models\Organization;
use App\Models\Period;
use App\Models\Qualification;
use App\Models\Skill;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        'Illuminate\Auth\Events\Verified' => [
            'App\Listeners\ActivarUsuario',
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LoginSuccess',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\LogoutSuccess',
        ],
        'Lab404\Impersonate\Events\TakeImpersonation' => [
            'App\Listeners\UserImpersonated',
        ],
        'Lab404\Impersonate\Events\LeaveImpersonation' => [
            'App\Listeners\UserImpersonatedEnded',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
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
