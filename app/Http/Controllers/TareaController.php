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
use Illuminate\Support\Facades\Log;

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
        $tarea->user->clearCache();

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

        $tareas = Tarea::whereIn('id', request('asignadas'))
            ->with(['user', 'actividad.cuestionarios', 'actividad.file_uploads', 'actividad.intellij_projects'])
            ->get();

        foreach ($tareas as $tarea) {
            $this->borrarTarea($tarea);
        }

        return redirect(route('profesor.tareas', ['user' => $user->id]));
    }

    public function fechaFinalizacionMultiple(User $user, Request $request)
    {
        Log::debug(request('asignadas'));

        $this->validate($request, [
            'asignadas' => 'required',
        ]);

        $tareas = Tarea::whereIn('id', request('asignadas'))->with('actividad')->get();
        foreach ($tareas as $tarea) {
            $actividad = $tarea->actividad;
            $actividad->fecha_entrega = request('fecha_override');
            $actividad->fecha_limite = $actividad->fecha_entrega?->addMinutes(10);
            $actividad->save();
        }

        return redirect(route('profesor.tareas', ['user' => $user->id]));
    }

    public function borrarMultipleActivas(Request $request)
    {
        $this->validate($request, [
            'asignadas' => 'required',
        ]);

        $tareas = Tarea::whereIn('id', request('asignadas'))
            ->with(['user', 'actividad.cuestionarios', 'actividad.file_uploads', 'actividad.intellij_projects'])
            ->get();

        foreach ($tareas as $tarea) {
            $this->borrarTarea($tarea);
        }

        return redirect(route('profesor.index'));
    }

    public function fechaFinalizacionMultipleActivas(Request $request)
    {
        $this->validate($request, [
            'asignadas' => 'required',
        ]);

        $tareas = Tarea::whereIn('id', request('asignadas'))->with('actividad')->get();
        foreach ($tareas as $tarea) {
            $actividad = $tarea->actividad;
            $actividad->fecha_entrega = request('fecha_override');
            $actividad->fecha_limite = $actividad->fecha_entrega?->addMinutes(10);
            $actividad->save();
        }

        return redirect(route('profesor.index'));
    }

    /**
     * @param Tarea $tarea
     */
    private function borrarTarea(Tarea $tarea): void
    {
        $tarea->loadMissing(['user', 'actividad.cuestionarios', 'actividad.file_uploads', 'actividad.intellij_projects']);

        $actividad_id = $tarea->actividad_id;
        $curso_id = Auth::user()->curso_actual()->id;

        Registro::create([
            'user_id' => $tarea->user->id,
            'tarea_id' => $tarea->id,
            'timestamp' => Carbon::now(),
            'estado' => 61,
            'curso_id' => $curso_id,
        ]);

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
                } catch (Exception) {
                    Log::error("No se ha podido borrar el repositorio", ['tarea' => $tarea->id, 'repo' => $repo['path_with_namespace']]);
                }
            }
        }

        $tarea->actividad->delete();
        $tarea->delete();
        $tarea->user->clearCache();

        // Al borrar la actividad compartida de un equipo, eliminar también
        // las tareas del resto de miembros que apuntan a la misma actividad.
        $tareas_equipo = Tarea::where('actividad_id', $actividad_id)
            ->whereNull('deleted_at')
            ->get();

        foreach ($tareas_equipo as $tarea_equipo) {
            Registro::create([
                'user_id' => $tarea_equipo->user_id,
                'tarea_id' => $tarea_equipo->id,
                'timestamp' => Carbon::now(),
                'estado' => 61,
                'curso_id' => $curso_id,
            ]);

            $tarea_equipo->delete();
            $tarea_equipo->user->clearCache();
        }
    }
}
