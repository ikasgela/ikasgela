<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function editar()
    {
        $user = Auth::user();
        $cursos = $user->cursos;

        setting()->setExtraColumns(['user_id' => $user->id]);

        return view('settings.edit', compact(['cursos', 'user']));
    }

    public function guardar(Request $request)
    {
        $this->validate($request, [
            'curso_id' => 'required',
        ]);

        setting()->setExtraColumns(['user_id' => Auth::user()->id]);
        setting(['curso_actual' => $request->input('curso_id')]);
        setting()->save();

        return redirect(route('settings.editar'));
    }
}
