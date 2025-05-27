<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Curso>
 */
class CursoFactory extends Factory
{
    protected $model = Curso::class;

    public function definition()
    {
        $name = fake()->sentence(2);

        return [
            'category_id' => Category::factory(),
            'nombre' => $name,
            'descripcion' => fake()->sentence(8),
            'slug' => Str::slug($name),
            'plazo_actividad' => 7,
        ];
    }
}
