<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\LinkCollection;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    protected $model = Link::class;

    public function definition()
    {
        return [
            'url' => $this->faker->url(),
            'descripcion' => $this->faker->sentence(6),
            'link_collection_id' => LinkCollection::factory(),
        ];
    }
}
