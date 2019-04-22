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
        $period = Period::where('name', '2018')->first();

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
    }
}
