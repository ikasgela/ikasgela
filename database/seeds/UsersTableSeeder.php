<?php

use App\Curso;
use App\Role;
use App\Team;
use App\User;
use Carbon\Carbon;
use GrahamCampbell\GitLab\Facades\GitLab;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $rol_admin = Role::where('name', 'admin')->first();
        $rol_profesor = Role::where('name', 'profesor')->first();
        $rol_alumno = Role::where('name', 'alumno')->first();

        $equipo = Team::find(1);

        $curso = Curso::find(1);

        $this->generarUsuario('Marc', 'test@ikasgela.com', [$rol_alumno], [$equipo], [$curso]);
        $this->generarUsuario('Noa', 'test2@ikasgela.com', [$rol_alumno], [$equipo], [$curso]);
        $this->generarUsuario('Lucía', 'profe@ikasgela.com', [$rol_profesor, $rol_admin], [], [$curso]);
        $this->generarUsuario('Administrador', 'admin@ikasgela.com', [$rol_admin], [], [$curso]);
    }

    private function generarUsuario(string $nombre, string $email, $roles, $equipos, $cursos): void
    {
        $usuario = User::generar_username($email);
        $password = App::environment('local') ? '12345Abcde' : bin2hex(openssl_random_pseudo_bytes(16));;   // REF: https://stackoverflow.com/a/21498316
        $fecha = Carbon::now();

        $user = new User();
        $user->name = $nombre;
        $user->email = $email;
        $user->username = $usuario;
        $user->email_verified_at = $fecha;
        $user->password = bcrypt($password);
        $user->tutorial = true;
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
}
