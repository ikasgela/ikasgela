<?php

namespace App\Http\Controllers;

use App\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CursoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cursos = Curso::all();

        return view('cursos.index', compact('cursos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cursos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required',
        ]);

        try {
            Curso::create([
                'nombre' => request('nombre'),
                'descripcion' => request('descripcion'),
                'slug' => Str::slug(request('nombre'))
            ]);
        } catch (\Exception $e) {
            // Slug repetido
        }

        return redirect(route('cursos.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Curso $curso
     * @return \Illuminate\Http\Response
     */
    public function show(Curso $curso)
    {
        return view('cursos.show', compact('curso'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Curso $curso
     * @return \Illuminate\Http\Response
     */
    public function edit(Curso $curso)
    {
        return view('cursos.edit', compact('curso'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Curso $curso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Curso $curso)
    {
        $this->validate($request, [
            'nombre' => 'required',
        ]);

        $curso->update([
            'nombre' => request('nombre'),
            'descripcion' => request('descripcion'),
            'slug' => strlen(request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('nombre'))
        ]);

        return redirect(route('cursos.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Curso $curso
     * @return \Illuminate\Http\Response
     */
    public function destroy(Curso $curso)
    {
        $curso->delete();

        return redirect(route('cursos.index'));
    }
}
