<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Exception;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Database\Seeder;

class UsersAdminSeeder extends Seeder
{
    public function run()
    {
        $rol_admin = Role::where('name', 'admin')->first();
        $rol_profesor = Role::where('name', 'profesor')->first();

        $egibide = Organization::where('slug', 'egibide')->first();
        $deusto = Organization::where('slug', 'deusto')->first();

        $this->generarUsuario('Ion', 'Jaureguialzo Sarasola',
            'ijaureguialzo@egibide.org', config('database.connections.mariadb.password'),
            [$rol_profesor, $rol_admin], [$egibide]
        );
        $this->generarUsuario('Ion', 'Jaureguialzo Sarasola',
            'ijaureguialzo@deusto.es', config('database.connections.mariadb.password'),
            [$rol_profesor, $rol_admin], [$deusto]
        );
    }

    private function generarUsuario(string $nombre, string $apellido, string $email, string $password, $roles, $organizations): void
    {
        $usuario = User::generar_username($email);

        $user = User::factory()->create([
            'name' => $nombre,
            'surname' => $apellido,
            'email' => $email,
            'username' => $usuario,
            'password' => bcrypt($password),
            'tutorial' => false,
        ]);

        foreach ($roles as $rol) {
            $user->roles()->attach($rol);
        }

        foreach ($organizations as $organization) {
            $user->organizations()->attach($organization);
        }

        setting()->setExtraColumns(['user_id' => $user->id]);
        setting(['_organization_id' => $organizations[0]->id]);
        setting(['_period_id' => $organizations[0]->current_period_id]);
        setting()->save();

        if (config('ikasgela.gitea_enabled')) {
            try {
                GiteaClient::borrar_usuario($usuario);
                $this->command->warn("  Usuario borrado: $email");
            } catch (Exception) {
                $this->command->error("  Usuario borrado: $email");
            }
            try {
                $nombre_completo = $nombre . ' ' . $apellido;
                GiteaClient::user($email, $usuario, $nombre_completo, $password);
                GiteaClient::unblock($email, $usuario);
                $this->command->info("  Usuario generado: $email");
            } catch (Exception) {
                $this->command->error("  Usuario no generado: $email");
            }
            $this->command->newLine();
        }
    }
}
