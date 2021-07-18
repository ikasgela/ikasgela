<?php

namespace Database\Factories;

use App\IntellijProject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class IntellijProjectFactory extends Factory
{
    protected $model = IntellijProject::class;

    public function definition()
    {
        $nombre = $this->faker->words(3, true);

        return [
            'repositorio' => 'root/test',
            'titulo' => $nombre,
            'host' => 'gitea',
        ];
    }
}
