<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $admin;
    protected $alumno;
    protected $profesor;
    protected $not_admin;
    protected $not_profesor;

    protected $not_alumno_profesor;
    protected $not_admin_profesor;

    public function crearUsuarios(): void
    {
        $rol_admin = Role::create(['name' => 'admin', 'description' => 'Administrador']);
        $rol_alumno = Role::create(['name' => 'alumno', 'description' => 'Alumno']);
        $rol_profesor = Role::create(['name' => 'profesor', 'description' => 'Profesor']);
        $rol_tutor = Role::create(['name' => 'tutor', 'description' => 'Tutor']);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($rol_admin);

        $this->alumno = User::factory()->create();
        $this->alumno->roles()->attach($rol_alumno);

        $this->profesor = User::factory()->create();
        $this->profesor->roles()->attach($rol_profesor);

        $this->not_admin = User::factory()->create();
        $this->not_admin->roles()->attach($rol_alumno);
        $this->not_admin->roles()->attach($rol_profesor);
        $this->not_admin->roles()->attach($rol_tutor);

        $this->not_profesor = User::factory()->create();
        $this->not_profesor->roles()->attach($rol_alumno);
        $this->not_profesor->roles()->attach($rol_admin);
        $this->not_profesor->roles()->attach($rol_tutor);

        $this->not_alumno_profesor = User::factory()->create();
        $this->not_alumno_profesor->roles()->attach($rol_admin);
        $this->not_alumno_profesor->roles()->attach($rol_tutor);

        $this->not_admin_profesor = User::factory()->create();
        $this->not_admin_profesor->roles()->attach($rol_alumno);
        $this->not_admin_profesor->roles()->attach($rol_tutor);
    }
}
