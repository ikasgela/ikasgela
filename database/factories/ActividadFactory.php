<?php

namespace Database\Factories;

use App\Models\Actividad;
use App\Models\Unidad;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Override;

/**
 * @extends Factory<Actividad>
 */
class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    public function definition()
    {
        $nombre = fake()->words(3, true);

        return [
            'unidad_id' => Unidad::factory(),
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ];
    }

    #[Override]
    public function configure()
    {
        return $this->afterCreating(function (Actividad $model) {
            $model->orden = $model->id;
            $model->save();
        });
    }
}
