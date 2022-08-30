<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\LinkCollection;
use Illuminate\Database\Seeder;

class LinkCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $link_collection = LinkCollection::factory()->create([
            'titulo' => 'Enlaces interesantes',
            'descripcion' => 'Algunos enlaces útiles.',
            'curso_id' => 1,
        ]);

        $enlace = Link::create([
            'url' => 'https://google.com',
            'descripcion' => 'Google',
            'link_collection_id' => $link_collection->id,
        ]);

        $enlace->orden = $enlace->id;
        $enlace->save();

        $enlace = Link::create([
            'url' => 'https://youtube.com',
            'link_collection_id' => $link_collection->id,
        ]);

        $enlace->orden = $enlace->id;
        $enlace->save();
    }
}
