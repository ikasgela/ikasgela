<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    public function index()
    {
        $user = Auth::user();

        if (!is_null($user)) {

            if (setting_usuario('_organization_id') == null) {
                $organization = Organization::where('slug', subdominio())->first();
                setting_usuario(['_organization_id' => $organization?->id]);
            }

            if (setting_usuario('_period_id') == null) {
                $organization = Organization::where('slug', subdominio())->first();
                setting_usuario(['_period_id' => $organization?->current_period_id]);
            }

            if (setting_usuario('curso_actual') == null) {
                $primer_curso = $user->cursos()->organizacionActual()->periodoActual()->first();
                setting_usuario(['curso_actual' => $primer_curso ? $primer_curso->id : null]);
            }

            if ($user->hasAnyRole(['profesor', 'admin'])) {
                return redirect(route('profesor.index'));
            } else if ($user->hasAnyRole(['tutor'])) {
                return redirect(route('tutor.index'));
            } else {
                return redirect(route('users.home'));
            }
        } else {
            return view('welcome');
        }
    }
}
