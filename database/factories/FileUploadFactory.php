<?php

namespace Database\Factories;

use App\FileUpload;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileUploadFactory extends Factory
{
    protected $model = FileUpload::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(6),
            'max_files' => 1
        ];
    }
}
