<?php

namespace App\Http\Controllers;

use App;
use App\Actividad;
use App\IntellijProject;
use Auth;
use GitLab;
use Illuminate\Http\Request;

class IntellijProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin', ['except' => 'fork']);
    }

    public function index()
    {
        $intellij_projects = IntellijProject::all();

        return view('intellij_projects.index', compact('intellij_projects'));
    }

    public function create()
    {
        return view('intellij_projects.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'repositorio' => 'required',
        ]);

        IntellijProject::create($request->all());

        return redirect(route('intellij_projects.index'));
    }

    public function show(IntellijProject $intellij_project)
    {
        return view('intellij_projects.show', compact('intellij_project'));
    }

    public function edit(IntellijProject $intellij_project)
    {
        return view('intellij_projects.edit', compact('intellij_project'));
    }

    public function update(Request $request, IntellijProject $intellij_project)
    {
        $this->validate($request, [
            'repositorio' => 'required',
        ]);

        $intellij_project->update($request->all());

        return redirect(route('intellij_projects.index'));
    }

    public function destroy(IntellijProject $intellij_project)
    {
        $intellij_project->delete();

        return redirect(route('intellij_projects.index'));
    }

    public function actividad(Actividad $actividad)
    {
        $intellij_projects = $actividad->intellij_projects()->get();

        $subset = $intellij_projects->pluck('id')->unique()->flatten()->toArray();
        $disponibles = IntellijProject::whereNotIn('id', $subset)->get();

        return view('intellij_projects.actividad', compact(['intellij_projects', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = IntellijProject::find($recurso_id);
            $actividad->intellij_projects()->attach($recurso);
        }

        return redirect(route('intellij_projects.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, IntellijProject $intellij_project)
    {
        $actividad->intellij_projects()->detach($intellij_project);
        return redirect(route('intellij_projects.actividad', ['actividad' => $actividad->id]));
    }

    public function fork(Actividad $actividad, IntellijProject $intellij_project, Request $request)
    {
        $username = Auth::user()->username;

        $proyecto = $intellij_project->gitlab();
        $ruta = $actividad->unidad->curso->slug
            . '-' . $actividad->unidad->slug
            . '-' . $actividad->slug
            . '-' . $proyecto['path'];

        $fork = $this->clonar_repositorio($proyecto['path_with_namespace'], $username, $ruta);

        if ($fork) {
            $actividad->intellij_projects()
                ->updateExistingPivot($intellij_project->id, ['fork' => $fork['path_with_namespace']]);
        } else {
            $request->session()->flash('clone_error_id', $actividad->id);
            $request->session()->flash('clone_error_status', 'Error al clonar el repositorio, contacta con el administrador.');
        }

        return redirect(route('users.home'));
    }

    /*
    private function borrar_proyectos($username)
    {
        // Borrar los proyectos del usuario (para pruebas)
        $usuario = GitLab::users()->all([
            'username' => $username
        ])[0];

        $proyectos = GitLab::users()->usersProjects($usuario['id']);

        foreach ($proyectos as $proyecto) {
            GitLab::projects()->remove($proyecto['id']);
        }
    }
    */

    private function clonar_repositorio($origen, $destino, $ruta, $nombre = null)
    {
        try {
            // Obtener el id del repositorio de origen
            $original = GitLab::projects()->show($origen);// Hacer el fork
            $fork = GitLab::projects()->fork($original['id'], [
                'namespace' => $destino,
                'name' => $nombre,
                'path' => $ruta
            ]);// Desconectarlo del repositorio original
            GitLab::projects()->removeForkRelation($fork['id']);// Convertirlo en privado
            $fork = GitLab::projects()->update($fork['id'], [
                'visibility' => 'private'
            ]);
            return $fork;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function copia()
    {
        // Solo los proyectos del root
        $proyectos = GitLab::projects()->all([
            'membership' => true
        ]);

        return view('intellij_projects.copia', compact('proyectos'));
    }

    public function duplicar(Request $request)
    {
        $origen = $request->input('origen');
        $destino = $request->input('destino');

        // Guardar en la sesión los datos para prerellenar el formulario
        session([
            'intellij_origen' => $origen,
            'intellij_destino' => $destino
        ]);

        $this->clonar_repositorio(
            $origen,
            $destino,
            $request->input('ruta'),
            $request->input('nombre')
        );

        return redirect(route('intellij_projects.copia'));
    }
}
