<?php

namespace Database\Seeders;

use App\Category;
use App\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $period = Period::whereHas('organization', function ($query) {
            $query->where('organizations.slug', 'egibide');
        })
            ->where('slug', '2019')
            ->first();

        $name = 'Programación';
        Category::factory()->create([
            'period_id' => $period->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = 'Sistemas';
        Category::factory()->create([
            'period_id' => $period->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $period = Period::whereHas('organization', function ($query) {
            $query->where('organizations.slug', 'deusto');
        })
            ->where('slug', '2019')
            ->first();

        $name = 'Programación';
        Category::factory()->create([
            'period_id' => $period->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $period = Period::whereHas('organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', '2019')
            ->first();

        $name = 'Programación';
        Category::factory()->create([
            'period_id' => $period->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
