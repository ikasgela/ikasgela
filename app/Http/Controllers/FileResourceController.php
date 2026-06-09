<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\FileResource;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileResourceController extends Controller
{
    use PaginarUltima;
    use FiltroCurso;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor|admin');
    }

    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $file_resources = $this->paginate_ultima($this->filtrar_por_curso($request, FileResource::class), config('ikasgela.pagination_medium'));

        return view('file_resources.index', compact(['file_resources', 'cursos']));
    }

    public function create()
    {
        return view('file_resources.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        FileResource::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        return retornar();
    }

    public function show(FileResource $file_resource)
    {
        $ids = $file_resource->files()->pluck('id')->toArray();

        return view('file_resources.show', compact(['file_resource', 'ids']));
    }

    public function edit(FileResource $file_resource)
    {
        return view('file_resources.edit', compact('file_resource'));
    }

    public function update(Request $request, FileResource $file_resource)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        $file_resource->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
        ]);

        return retornar();
    }

    public function destroy(FileResource $file_resource)
    {
        foreach ($file_resource->files as $file) {
            $file->delete();
        }

        $file_resource->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $file_resources = $actividad->file_resources()->get();

        $subset = $file_resources->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(FileResource::where('curso_id', $curso_actual)->whereNotIn('id', $subset));

        return view('file_resources.actividad', compact(['file_resources', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        $recursos = FileResource::whereIn('id', request('seleccionadas'))->get()->keyBy('id');
        foreach (request('seleccionadas') as $recurso_id) {
            $actividad->file_resources()->attach($recursos[$recurso_id], ['orden' => Str::orderedUuid()]);
        }

        return redirect(route('file_resources.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, FileResource $file_resource)
    {
        $actividad->file_resources()->detach($file_resource);
        return redirect(route('file_resources.actividad', ['actividad' => $actividad->id]));
    }

    public function toggle_titulo_visible(Actividad $actividad, FileResource $file_resource)
    {
        $pivote = $file_resource->pivote($actividad);
        $file_resource_id = $pivote->file_resource_id;

        $pivote->titulo_visible = !$pivote->titulo_visible;
        // Use updateExistingPivot to trigger cache invalidation
        $actividad->file_resources()->updateExistingPivot($file_resource_id, [
            'titulo_visible' => $pivote->titulo_visible,
        ]);

        return back();
    }

    public function toggle_descripcion_visible(Actividad $actividad, FileResource $file_resource)
    {
        $pivote = $file_resource->pivote($actividad);
        $file_resource_id = $pivote->file_resource_id;

        $pivote->descripcion_visible = !$pivote->descripcion_visible;
        // Use updateExistingPivot to trigger cache invalidation
        $actividad->file_resources()->updateExistingPivot($file_resource_id, [
            'descripcion_visible' => $pivote->descripcion_visible,
        ]);

        return back();
    }

    public function duplicar(FileResource $file_resource)
    {
        $file_resource->duplicar(null);

        return redirect(route('file_resources.index'));
    }
}
