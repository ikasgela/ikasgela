<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(OrganizationsTableSeeder::class);
        $this->call(PeriodsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(CursosTableSeeder::class);
        $this->call(UnidadesTableSeeder::class);

        $this->call(FeedbacksTableSeeder::class);

        $this->call(GroupsTableSeeder::class);
        $this->call(TeamsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);

        $this->call(YoutubeVideosTableSeeder::class);
        $this->call(IntellijProjectsTableSeeder::class);
        $this->call(MarkdownTextsTableSeeder::class);
        $this->call(CuestionariosTableSeeder::class);
        $this->call(PreguntasTableSeeder::class);
        $this->call(ItemsTableSeeder::class);
        $this->call(FileUploadsTableSeeder::class);
        $this->call(FileResourceSeeder::class);

        $this->call(ActividadesTableSeeder::class);
        $this->call(TareasTableSeeder::class);
        $this->call(RegistrosTableSeeder::class);

        $this->call(QualificationsTableSeeder::class);

    }
}
