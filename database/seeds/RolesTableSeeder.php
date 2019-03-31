<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();
        $role->name = 'admin';
        $role->description = 'Administrador';
        $role->save();

        $role = new Role();
        $role->name = 'profesor';
        $role->description = 'Profesor';
        $role->save();

        $role = new Role();
        $role->name = 'alumno';
        $role->description = 'Alumno';
        $role->save();
    }
}
