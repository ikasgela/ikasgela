<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Curso;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use App\YoutubeVideo;
use Illuminate\Http\Request;

class YoutubeVideoController extends Controller
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

        $youtube_videos = $this->filtrar_por_curso($request, YoutubeVideo::class)->get();

        return view('youtube_videos.index', compact(['youtube_videos', 'cursos']));
    }

    public function create()
    {
        return view('youtube_videos.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'codigo' => 'required',
        ]);

        YoutubeVideo::create($request->all());

        return retornar();
    }

    public function show(YoutubeVideo $youtube_video)
    {
        return abort(501);
    }

    public function edit(YoutubeVideo $youtube_video)
    {
        return view('youtube_videos.edit', compact('youtube_video'));
    }

    public function update(Request $request, YoutubeVideo $youtube_video)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'codigo' => 'required',
        ]);

        $youtube_video->update($request->all());

        return retornar();
    }

    public function destroy(YoutubeVideo $youtube_video)
    {
        $youtube_video->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $youtube_videos = $actividad->youtube_videos()->get();

        $subset = $youtube_videos->pluck('id')->unique()->flatten()->toArray();
        $disponibles = $this->paginate_ultima(YoutubeVideo::whereNotIn('id', $subset));

        return view('youtube_videos.actividad', compact(['youtube_videos', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = YoutubeVideo::find($recurso_id);
            $actividad->youtube_videos()->attach($recurso);
        }

        return redirect(route('youtube_videos.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, YoutubeVideo $youtube_video)
    {
        $actividad->youtube_videos()->detach($youtube_video);
        return redirect(route('youtube_videos.actividad', ['actividad' => $actividad->id]));
    }
}
