<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $rol_admin = Role::where('name', 'admin')->first();
        $rol_profesor = Role::where('name', 'profesor')->first();
        $rol_alumno = Role::where('name', 'alumno')->first();
        $rol_tutor = Role::where('name', 'tutor')->first();

        $equipo = Team::whereHas('group.period.organization', function ($query) {
            $query->where('organizations.slug', 'egibide');
        })
            ->where('slug', 'todos')
            ->first();

        $curso_ikasgela = Curso::find(1);
        $curso_deusto = Curso::find(2);
        $curso_egibide = Curso::find(3);

        $ikasgela = Organization::where('slug', 'ikasgela')->first();
        $egibide = Organization::where('slug', 'egibide')->first();
        $deusto = Organization::where('slug', 'deusto')->first();

        $this->generarUsuario('Marc', 'Watney', 'marc@ikasgela.com', [$rol_alumno], [], [$curso_ikasgela], [$ikasgela], "B");
        $this->generarUsuario('Noa', 'Ark', 'noa@ikasgela.com', [$rol_alumno], [], [$curso_ikasgela], [$ikasgela], "A");
        $this->generarUsuario('Lucía', '', 'lucia@ikasgela.com', [$rol_profesor, $rol_admin, $rol_tutor], [], [$curso_ikasgela], [$ikasgela], "B");

        $this->generarUsuario('Profesor', '', 'profesor@ikasgela.com', [$rol_profesor], [], [$curso_ikasgela], [$ikasgela], "dusk");
        $this->generarUsuario('Tutor', '', 'tutor@ikasgela.com', [$rol_tutor], [], [$curso_ikasgela], [$ikasgela], "dusk");
        $this->generarUsuario('Admin', '', 'admin@ikasgela.com', [$rol_admin], [], [$curso_ikasgela], [$ikasgela], "dusk");
    }

    private function generarUsuario(string $nombre, string $apellido, string $email, $roles, $equipos, $cursos, $organizations, $tags): void
    {
        $usuario = User::generar_username($email);
        $password = App::environment(['local', 'testing']) ? '12345Abcde' : bin2hex(openssl_random_pseudo_bytes(16));;   // REF: https://stackoverflow.com/a/21498316
        $fecha = Carbon::now();

        $user = new User();
        $user->name = $nombre;
        $user->surname = $apellido;
        $user->email = $email;
        $user->username = $usuario;
        $user->email_verified_at = $fecha;
        $user->password = bcrypt($password);
        $user->tutorial = true;
        $user->tags = $tags;
        $user->save();

        foreach ($roles as $rol) {
            $user->roles()->attach($rol);
        }

        foreach ($equipos as $equipo) {
            $user->teams()->attach($equipo);
        }

        foreach ($cursos as $curso) {
            $user->cursos()->attach($curso);
        }

        foreach ($organizations as $organization) {
            $user->organizations()->attach($organization);
        }

        setting()->setExtraColumns(['user_id' => $user->id]);
        setting(['_organization_id' => $organizations[0]->id]);
        setting(['_period_id' => $organizations[0]->current_period_id]);
        $primer_curso = $cursos[0];
        setting(['curso_actual' => $primer_curso ? $primer_curso->id : null]);
        setting()->save();

        if (config('ikasgela.gitlab_enabled')) {
            if (config('app.env', 'local') != 'testing') {
                echo "  INFO: Usuario generado: $nombre - $email - $password\n";

                try {
                    $usuarios = GitLab::users()->all([
                        'search' => $email
                    ]);
                    foreach ($usuarios as $borrar) {
                        GitLab::users()->remove($borrar['id']);
                    }
                } catch (\Exception $e) {
                }

                sleep(2);   // Si no, no de la tiempo a borrar y da error

                // Crear el usuario de GitLab
                try {
                    GitLab::users()->create($email, $password, [
                        'name' => $nombre,
                        'username' => $usuario,
                        'skip_confirmation' => true
                    ]);
                    echo "  INFO: Usuario de GitLab creado.\n";
                } catch (\Exception $e) {
                    echo "  ERROR: No se ha podido crear el usuario de GitLab asociado...\n";
                }
            }
        }

        if (config('ikasgela.gitea_enabled')) {
            try {
                GiteaClient::borrar_usuario($usuario);
                echo "  INFO: Usuario borrado: $nombre - $email - $password\n";
            } catch (\Exception $e) {
                echo "  ERROR: Usuario no borrado: $nombre - $email - $password\n";
            }
        }

        if (config('ikasgela.gitea_enabled')) {
            try {
                $nombre_completo = $nombre . ' ' . $apellido;
                GiteaClient::user($email, $usuario, $nombre_completo, $password);
                GiteaClient::unblock($email, $usuario);
                echo "  INFO: Usuario generado: $nombre_completo - $email - $password\n";
            } catch (\Exception $e) {
                echo "  ERROR: Usuario no generado: $nombre_completo - $email - $password\n";
            }
        }
    }
}
