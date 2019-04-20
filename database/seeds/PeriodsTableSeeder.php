<?php

use App\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PeriodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $name = '2018';
        factory(Period::class)->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = '2019';
        factory(Period::class)->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
