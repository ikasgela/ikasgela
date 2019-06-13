<?php

use App\Category;
use App\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
        factory(Category::class)->create([
            'period_id' => $period->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = 'Sistemas';
        factory(Category::class)->create([
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
        factory(Category::class)->create([
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
        factory(Category::class)->create([
            'period_id' => $period->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
