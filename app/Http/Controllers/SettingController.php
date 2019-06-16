<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function editar()
    {
        $user = Auth::user();

        setting()->setExtraColumns(['user_id' => $user->id]);

        $cursos = $user->cursos()->orderBy('nombre')->get();

        $organizations = $user->organizations()->orderBy('name')->get();

        $periods = Period::where('organization_id', setting_usuario('_organization_id'))->orderBy('name')->get();

        return view('settings.edit', compact(['user', 'cursos', 'organizations', 'periods']));
    }

    public function guardar(Request $request)
    {
        if (!is_null($request->input('curso_id'))) {
            setting_usuario(['curso_actual' => $request->input('curso_id')]);
        }

        if (!is_null($request->input('organization_id'))) {
            $organization = Organization::find($request->input('organization_id'));
            setting_usuario(['_organization_id' => $organization->id]);
            setting_usuario(['_period_id' => $organization->current_period_id]);
            setting_usuario(['curso_actual' => null]);
        }

        if (!is_null($request->input('period_id'))) {
            setting_usuario(['_period_id' => $request->input('period_id')]);
            setting_usuario(['_organization_id' => setting_usuario('_organization_id')]);
            setting_usuario(['curso_actual' => null]);
        }

        return redirect(route('settings.editar'));
    }
}
