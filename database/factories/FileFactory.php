<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\FileUpload;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition()
    {
        return [
            // REF: Generar ruta de archivo: https://github.com/fzaninotto/Faker/issues/1472#issuecomment-384364762
            'path' => '/' . implode('/', $this->faker->words($this->faker->numberBetween(0, 4))),
            'title' => $this->faker->words(3, true),
            'size' => $this->faker->numberBetween(0, 1_000_000_000),
            'file_upload_id' => FileUpload::factory(),
            'user_id' => User::factory(),
        ];
    }
}
