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

        $organizations = $user->organizations()->orderBy('name')->get();

        $filtro = $user->organizations()->pluck('organization_id')->unique()->flatten()->toArray();
        $periods = Period::whereIn('id', $filtro)->orderBy('name')->get();

        $cursos = $user->cursos()->orderBy('nombre')->get();

        setting()->setExtraColumns(['user_id' => $user->id]);

        return view('settings.edit', compact(['user', 'cursos', 'organizations', 'periods']));
    }

    public function guardar(Request $request)
    {
        setting()->setExtraColumns(['user_id' => Auth::user()->id]);

        if (!is_null($request->input('organization_actual')))
            setting(['organization_actual' => $request->input('organization_id')]);
        if (!is_null($request->input('period_actual')))
            setting(['period_actual' => $request->input('period_id')]);
        if (!is_null($request->input('curso_id')))
            setting(['curso_actual' => $request->input('curso_id')]);

        setting()->save();

        return redirect(route('settings.editar'));
    }
}
