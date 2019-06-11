<?php

use App\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $name = 'ikasgela';
        factory(Organization::class)->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = 'Egibide';
        factory(Organization::class)->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = 'Deusto';
        factory(Organization::class)->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
