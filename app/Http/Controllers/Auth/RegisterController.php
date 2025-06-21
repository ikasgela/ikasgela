<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NuevoUsuario;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        switch (subdominio()) {
            case 'egibide':
                $dominios = 'egibide.org,ikasle.egibide.org';
                $validator = 'allowed_domains';
                break;
            case 'deusto':
                $dominios = 'deusto.es,opendeusto.es';
                $validator = 'allowed_domains';
                break;
            default:
                $dominios = 'egibide.org,ikasle.egibide.org,deusto.es,opendeusto.es';
                $validator = 'forbidden_domains';
                break;
        }

        if (config('app.debug'))
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'surname' => 'string|nullable|max:255',
                'email' => "required|string|email|$validator:$dominios|max:255|unique:users|confirmed",
                'password' => 'required|string|min:8|confirmed',
            ]);
        else
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'surname' => 'string|nullable|max:255',
                'email' => "required|string|email|$validator:$dominios|max:255|unique:users|confirmed",
                'password' => 'required|string|min:8|confirmed',
            ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return User
     */
    protected function create(array $data)
    {
        $organization = Organization::where('slug', subdominio())->first();

        if (!$organization->isRegistrationOpen()) {
            abort(403, __("Sorry, you are not authorized to access this page."));
        }

        $organization->decrement('seats', 1);

        // Generar el nombre de usuario a partir del email
        $nombre_usuario = User::generar_username($data['email']);

        // Crear el usuario de Gitea y dejarlo bloqueado
        if (config('ikasgela.gitea_enabled')) {
            try {
                $nombre_completo = $data['name'] . ' ' . $data['surname'];
                GiteaClient::user($data['email'], $nombre_usuario, $nombre_completo, $data['password']);
                GiteaClient::block($data['email'], $nombre_usuario);
            } catch (Exception $e) {
                Log::error('Gitea: Error al crear el usuario.', [
                    'username' => $nombre_usuario,
                    'exception' => $e->getMessage()
                ]);
            }
        }

        // Crear el usuario de Laravel
        $laravel = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'username' => $nombre_usuario,
            'password' => Hash::make($data['password']),
            'tutorial' => true,
            'last_active' => Carbon::now(),
            'tags' => $organization->current_period()->slug,
        ]);

        $laravel
            ->roles()
            ->attach(Role::where('name', 'alumno')->first());

        $laravel
            ->organizations()
            ->attach($organization);

        activity()
            ->causedBy($laravel)
            ->log('Nuevo usuario');

        foreach ($organization->users()->rolAdmin()->get() as $admin) {
            Mail::to($admin)->queue(new NuevoUsuario($laravel));
        }

        return $laravel;
    }
}
