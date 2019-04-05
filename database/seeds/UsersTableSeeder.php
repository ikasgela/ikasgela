<?php

use App\Role;
use App\User;
use Carbon\Carbon;
use GrahamCampbell\GitLab\Facades\GitLab;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rol_admin = Role::where('name', 'admin')->first();
        $rol_profesor = Role::where('name', 'profesor')->first();
        $rol_alumno = Role::where('name', 'alumno')->first();

        $this->generarUsuario('Marc', 'test@ikasgela.com', [$rol_alumno]);
        $this->generarUsuario('Noa', 'test2@ikasgela.com', [$rol_alumno]);
        $this->generarUsuario('Lucía', 'profe@ikasgela.com', [$rol_profesor]);
        $this->generarUsuario('Administrador', 'admin@ikasgela.com', [$rol_admin]);
    }

    /**
     * @param string $email
     * @param string $nombre
     */
    private function generarUsuario(string $nombre, string $email, $roles): void
    {
        $usuario = User::generar_username($email);
        $password = App::environment('local') ? '12345Abcde' : bin2hex(openssl_random_pseudo_bytes(16));;   // REF: https://stackoverflow.com/a/21498316
        $fecha = Carbon::now();

        echo "  INFO: Usuario generado: $nombre - $email - $password\n";

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
