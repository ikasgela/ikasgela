<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use App\Models\Tarea;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class TareaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(Tarea $tarea)
    {
        return view('tareas.edit', compact('tarea'));
    }

    public function update(Request $request, Tarea $tarea)
    {
        $this->validate($request, [
            'estado' => 'required',
        ]);

        $tarea->update($request->input());

        return redirect(route('profesor.tareas', ['user' => $tarea->user->id]));
    }

    public function destroy(User $user, Tarea $tarea)
    {
        $this->borrarTarea($tarea);

        return redirect(route('profesor.tareas', ['user' => $user->id]));
    }

    public function borrarMultiple(User $user, Request $request)
    {
        $this->validate($request, [
            'asignadas' => 'required',
        ]);

        foreach (request('asignadas') as $tarea_id) {
            $this->borrarTarea(Tarea::find($tarea_id));
        }

        return redirect(route('profesor.tareas', ['user' => $user->id]));
    }

    /**
     * @param Tarea $tarea
     */
    private function borrarTarea(Tarea $tarea): void
    {
        $registro = new Registro();
        $registro->user_id = $tarea->user->id;
        $registro->tarea_id = $tarea->id;
        $registro->timestamp = Carbon::now();
        $registro->estado = 61;
        $registro->curso_id = Auth::user()->curso_actual()->id;
        $registro->save();

        foreach ($tarea->actividad->cuestionarios as $cuestionario) {
            $cuestionario->delete();
        }

        foreach ($tarea->actividad->file_uploads as $file_upload) {
            $file_upload->delete_with_files();
        }

        foreach ($tarea->actividad->intellij_projects as $intellij_project) {
            if ($intellij_project->isForked()) {
                $repo = $intellij_project->repository();
                try {
                    GiteaClient::borrar_repo($repo['id']);
                } catch (Exception $e) {
                    Log::error("No se ha podido borrar el repositorio", ['tarea' => $tarea->id, 'repo' => $repo['path_with_namespace']]);
                }
            }
        }

        $tarea->actividad->delete();

        $tarea->delete();
    }
}
