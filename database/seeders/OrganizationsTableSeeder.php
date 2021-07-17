<?php

namespace Database\Seeders;

use App\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $name = 'ikasgela';
        Organization::factory()->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = 'Egibide';
        Organization::factory()->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = 'Deusto';
        Organization::factory()->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
