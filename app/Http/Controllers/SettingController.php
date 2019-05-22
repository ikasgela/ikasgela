<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Setting;

class SettingController extends Controller
{
    public function editar()
    {
        $user = Auth::user();
        $cursos = $user->cursos;

        return view('settings.edit', compact(['cursos', 'user']));
    }

    public function guardar(Request $request)
    {
        $this->validate($request, [
            'curso_id' => 'required',
        ]);

        $user = Auth::user();

        Setting::set($user->id . '.curso_actual', $request->input('curso_id'));
        Setting::save();

        return redirect(route('settings.editar'));
    }
}
