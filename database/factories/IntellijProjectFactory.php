<?php

namespace Database\Factories;

use App\Models\IntellijProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IntellijProject>
 */
class IntellijProjectFactory extends Factory
{
    protected $model = IntellijProject::class;

    public function definition()
    {
        $nombre = fake()->words(3, true);

        return [
            'repositorio' => 'root/test',
            'titulo' => $nombre,
            'host' => 'gitea',
            'open_with' => 'idea',
        ];
    }
}
