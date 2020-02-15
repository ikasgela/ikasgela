<?php

namespace App\Http\Controllers;

use App;
use App\Actividad;
use App\Gitea\GiteaClient;
use App\IntellijProject;
use App\Jobs\ForkGiteaRepo;
use App\Jobs\ForkGitLabRepo;
use Auth;
use Cache;
use GitLab;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;

class IntellijProjectController extends Controller
{
    use App\Traits\ClonarRepoGitea;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin', ['except' => ['fork', 'is_forking']]);
    }

    public function index()
    {
        memorizar_ruta();

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
        $actividad->intellij_projects()->updateExistingPivot($intellij_project->id, ['is_forking' => true]);

        if (!App::environment('testing')) {
            if ($intellij_project->host == 'gitlab') {
                ForkGitLabRepo::dispatch($actividad, $intellij_project, Auth::user()); //->delay(10);
            } else {
                ForkGiteaRepo::dispatch($actividad, $intellij_project, Auth::user());
            }
        } else
            ForkGitLabRepo::dispatchNow($actividad, $intellij_project, Auth::user());

        return redirect(route('users.home'));
    }

    public function is_forking(Actividad $actividad, IntellijProject $intellij_project, Request $request)
    {
        $proyecto = $actividad->intellij_projects()->find($intellij_project->id);

        if (!$proyecto->isForked())
            return $proyecto->isForking();
        else
            return 2;   // 0 sin clonar, 1 clonando y 2 completado
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

    public function copia()
    {
        // Solo los proyectos del root
//        $proyectos = GitLab::projects()->all([
//            'membership' => true
//        ]);

        $proyectos = [];

        return view('intellij_projects.copia', compact('proyectos'));
    }

    public function borrar($id)
    {
        GitLab::projects()->remove($id);

        return back();
    }

    public function duplicar(Request $request)
    {
        $origen = $request->input('origen');
        $destino = $request->input('destino');
        $ruta = $request->input('ruta');
        $nombre = $request->input('nombre');

        // Guardar en la sesión los datos para prerellenar el formulario
        session([
            'intellij_origen' => $origen,
            'intellij_destino' => $destino
        ]);

        try {
            $proyecto = GiteaClient::repo($origen);

            if (empty($ruta)) {
                $ruta = $proyecto['name'];
            }

//            dump($proyecto);
//            dump($destino);
//            dump($ruta);
//            dd();

            $this->clonar_repositorio($proyecto['path_with_namespace'], $destino, Str::slug($ruta));
        } catch (\Exception $e) {
            Log::error($e);
        }

        return redirect(route('intellij_projects.copia'));
    }

    public function testGitLab(Request $request)
    {
        $n = 0 + $request->input('n');

        for ($i = 0; $i < $n; $i++) {
            $actividad = factory(Actividad::class)->create();
            $intellij_project = factory(IntellijProject::class)->create();

            $actividad->intellij_projects()->attach($intellij_project, ['is_forking' => true]);

            Auth::user()->actividades()->attach($actividad);

            ForkGitLabRepo::dispatch($actividad, $intellij_project, Auth::user(), true);
        }

        return 'ok';
    }

    public function lock(IntellijProject $intellij_project, Actividad $actividad)
    {
        $proyecto = $actividad->intellij_projects()->wherePivot('intellij_project_id', $intellij_project->id)->first();

        $proyecto->archive();

        return back();
    }

    public function unlock(IntellijProject $intellij_project, Actividad $actividad)
    {
        $proyecto = $actividad->intellij_projects()->wherePivot('intellij_project_id', $intellij_project->id)->first();

        $proyecto->unarchive();

        return back();
    }
}
