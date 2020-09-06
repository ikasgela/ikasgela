<?php

use App\FileResource;
use Illuminate\Database\Seeder;

class FileResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(FileResource::class)->create([
            'titulo' => 'Presentaciones',
            'descripcion' => 'Archivos PDF con las presentaciones.',
        ]);
    }
}
