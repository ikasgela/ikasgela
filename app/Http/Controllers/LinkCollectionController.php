<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\LinkCollection;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LinkCollectionController extends Controller
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

        $link_collections = $this->filtrar_por_curso($request, LinkCollection::class)->get();

        return view('link_collections.index', compact(['link_collections', 'cursos']));
    }

    public function create()
    {
        return view('link_collections.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        LinkCollection::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        return retornar();
    }

    public function show(LinkCollection $link_collection)
    {
        $ids = $link_collection->links()->pluck('id')->toArray();

        return view('link_collections.show', compact(['link_collection', 'ids']));
    }

    public function edit(LinkCollection $link_collection)
    {
        return view('link_collections.edit', compact('link_collection'));
    }

    public function update(Request $request, LinkCollection $link_collection)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        $link_collection->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
        ]);

        return retornar();
    }

    public function destroy(LinkCollection $link_collection)
    {
        $link_collection->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $link_collections = $actividad->link_collections()->get();

        $subset = $link_collections->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(LinkCollection::where('curso_id', $curso_actual)->whereNotIn('id', $subset));

        return view('link_collections.actividad', compact(['link_collections', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = LinkCollection::find($recurso_id);
            $actividad->link_collections()->attach($recurso, ['orden' => Str::orderedUuid()]);
        }

        return redirect(route('link_collections.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, LinkCollection $link_collection)
    {
        $actividad->link_collections()->detach($link_collection);
        return redirect(route('link_collections.actividad', ['actividad' => $actividad->id]));
    }

    public function toggle_titulo_visible(Actividad $actividad, LinkCollection $link_collection)
    {
        $pivote = $link_collection->pivote($actividad);

        $pivote->titulo_visible = !$pivote->titulo_visible;
        $pivote->save();

        return back();
    }

    public function toggle_descripcion_visible(Actividad $actividad, LinkCollection $link_collection)
    {
        $pivote = $link_collection->pivote($actividad);

        $pivote->descripcion_visible = !$pivote->descripcion_visible;
        $pivote->save();

        return back();
    }

    public function duplicar(LinkCollection $link_collection)
    {
        $clon = $link_collection->duplicate();
        $clon->titulo = $clon->titulo . " (" . __("Copy") . ')';
        $clon->save();

        return redirect(route('link_collections.index'));
    }
}
