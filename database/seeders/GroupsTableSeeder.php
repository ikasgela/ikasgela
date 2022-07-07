<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Group;
use App\Models\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $period = Period::whereHas('organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', now()->year)
            ->first();

        $name = 'Alumnos';
        $group = Group::factory()->create([
            'period_id' => $period->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'programacion')
            ->first();

        $group->cursos()->sync($curso);
    }
}
