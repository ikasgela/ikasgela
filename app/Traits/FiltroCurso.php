<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait FiltroCurso
{
    public function filtrar_por_curso(Request $request, string $model): mixed
    {
        $request->validate([
            'curso_id' => 'numeric|integer',
        ]);

        if (request('curso_id') >= -1) {
            session(['filtrar_curso_actual' => request('curso_id')]);
        } else if (empty(session('filtrar_curso_actual'))) {
            session(['filtrar_curso_actual' => Auth::user()->curso_actual()?->id]);
        }

        if (session('filtrar_curso_actual') == -1) {
            $results = $model::all();
        } else {
            $results = $model::where('curso_id', session('filtrar_curso_actual'))->get();
        }

        return $results;
    }
}
