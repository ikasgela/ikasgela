<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\FileUpload;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition()
    {
        return [
            // REF: Generar ruta de archivo: https://github.com/fzaninotto/Faker/issues/1472#issuecomment-384364762
            'path' => '/' . implode('/', fake()->words(fake()->numberBetween(0, 4))),
            'title' => fake()->words(3, true),
            'size' => fake()->numberBetween(0, 1_000_000_000),
            'uploadable_id' => FileUpload::factory(),
            'uploadable_type' => FileUpload::class,
            'user_id' => User::factory(),
        ];
    }
}
