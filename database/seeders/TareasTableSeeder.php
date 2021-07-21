<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\User;
use Illuminate\Database\Seeder;

class TareasTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $usuario = User::where('email', 'marc@ikasgela.com')->first();

        $actividad = Actividad::whereHas('unidad.curso.category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('nombre', 'Tarea de bienvenida')
            ->where('plantilla', true)
            ->first();

        $clon = $actividad->duplicate();
        $clon->plantilla_id = $actividad->id;
        $clon->final = true;
        $clon->save();
        $usuario->actividades()->attach($clon, ['puntuacion' => 0]);
    }
}
