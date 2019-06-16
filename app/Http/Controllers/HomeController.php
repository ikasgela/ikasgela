<?php

namespace App\Http\Controllers;

use App\Organization;
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

    public function index()
    {
        if (!is_null($this->getAuthUser())) {

            if (setting_usuario('_organization_id') == null) {
                $organization = Organization::where('slug', subdominio())->first();
                setting_usuario(['_organization_id' => $organization->id]);
                setting_usuario(['_period_id' => $organization->current_period_id]);
            }

            if (setting_usuario('curso_actual') == null) {
                $primer_curso = $this->getAuthUser()->cursos()->first();
                setting_usuario(['curso_actual' => $primer_curso->id]);
            }

            if ($this->getAuthUser()->hasRole('profesor')) {
                return redirect(route('profesor.index'));
            } else {
                return redirect(route('users.home'));
            }
        } else {
            return view('welcome');
        }
    }
}
