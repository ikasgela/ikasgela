<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\TestResult;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TestResultController extends Controller
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

        $test_results = $this->filtrar_por_curso($request, TestResult::class)->plantilla()->get();

        return view('test_results.index', compact(['test_results', 'cursos']));
    }

    public function create()
    {
        return view('test_results.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'num_preguntas' => 'required',
            'valor_correcta' => 'required',
            'valor_incorrecta' => 'required',
        ]);

        TestResult::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
            'completado' => $request->has('completado'),
            'num_preguntas' => $request->input('num_preguntas'),
            'valor_correcta' => $request->input('valor_correcta'),
            'valor_incorrecta' => $request->input('valor_incorrecta'),
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        return retornar();
    }

    public function show(TestResult $test_result)
    {
        return view('test_results.show', compact(['test_result']));
    }

    public function edit(TestResult $test_result)
    {
        return view('test_results.edit', compact(['test_result']));
    }

    public function update(Request $request, TestResult $test_result)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'num_preguntas' => 'required',
            'valor_correcta' => 'required',
            'valor_incorrecta' => 'required',
        ]);

        $test_result->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
            'completado' => $request->has('completado'),
            'num_preguntas' => $request->input('num_preguntas'),
            'valor_correcta' => $request->input('valor_correcta'),
            'valor_incorrecta' => $request->input('valor_incorrecta'),
            'num_correctas' => $request->input('num_correctas'),
            'num_incorrectas' => $request->input('num_incorrectas'),
        ]);

        return retornar();
    }

    public function destroy(TestResult $test_result)
    {
        $test_result->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $test_results = $actividad->test_results()->get();

        $subset = $test_results->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(TestResult::where('curso_id', $curso_actual)->where('plantilla', true)->whereNotIn('id', $subset));

        return view('test_results.actividad', compact(['test_results', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = TestResult::find($recurso_id);
            $actividad->test_results()->attach($recurso, [
                'orden' => Str::orderedUuid(),
                'columnas' => 12,
            ]);
        }

        return redirect(route('test_results.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, TestResult $test_result)
    {
        $actividad->test_results()->detach($test_result);
        return redirect(route('test_results.actividad', ['actividad' => $actividad->id]));
    }

    public function duplicar(TestResult $test_result)
    {
        $test_result->duplicar(null);

        return redirect(route('test_results.index'));
    }
}
