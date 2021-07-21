<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CursoFactory extends Factory
{
    protected $model = Curso::class;

    public function definition()
    {
        $name = $this->faker->sentence(2);

        return [
            'category_id' => Category::factory(),
            'nombre' => $name,
            'descripcion' => $this->faker->sentence(8),
            'slug' => Str::slug($name),
            'plazo_actividad' => 7,
        ];
    }
}
