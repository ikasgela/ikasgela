<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function getAuthUser()
    {
        return Auth::user();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home()
    {
        /*
         * 10 -> Nueva
         * 11 -> Oculta
         * 20 -> Aceptada
         * 30 -> Enviada
         * 40 -> Revisada: OK
         * 41 -> Revisada: ERROR
         * 50 -> Terminada
         * 60 -> Archivada
         * */

        $user = $this->getAuthUser();

        $actividades = $user->actividades_asignadas();

        $num_actividades = count($actividades);
        if ($num_actividades > 0)
            session(['num_actividades' => $num_actividades]);
        else
            session()->forget('num_actividades');

        return view('users.home', compact(['actividades', 'user']));
    }

    public function index()
    {
        if (!is_null($this->getAuthUser()))
            return redirect(route('users.home'));
        else
            return view('welcome');
    }
}
