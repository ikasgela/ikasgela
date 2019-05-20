<?php

namespace App\Http\Controllers;

use App\Category;
use App\Curso;
use App\Qualification;
use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CursoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $cursos = Curso::all();

        return view('cursos.index', compact('cursos'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $qualifications = Qualification::orderBy('name')->get();

        return view('cursos.create', compact(['categories', 'qualifications']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'nombre' => 'required',
        ]);

        try {
            Curso::create([
                'category_id' => request('category_id'),
                'nombre' => request('nombre'),
                'descripcion' => request('descripcion'),
                'slug' => Str::slug(request('nombre')),
                'qualification_id' => request('qualification_id'),
            ]);
        } catch (\Exception $e) {
            // Slug repetido
        }

        return redirect(route('cursos.index'));
    }

    public function show(Curso $curso)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Curso $curso
     * @return \Illuminate\Http\Response
     */
    public function edit(Curso $curso)
    {
        $categories = Category::orderBy('name')->get();
        $qualifications = Qualification::orderBy('name')->get();

        return view('cursos.edit', compact(['curso', 'categories', 'qualifications']));
    }

    public function update(Request $request, Curso $curso)
    {
        $this->validate($request, [
            'nombre' => 'required',
            'category_id' => 'required',
        ]);

        $curso->update([
            'category_id' => request('category_id'),
            'nombre' => request('nombre'),
            'descripcion' => request('descripcion'),
            'slug' => strlen(request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('nombre')),
            'qualification_id' => request('qualification_id'),
        ]);

        return redirect(route('cursos.index'));
    }

    public function destroy(Curso $curso)
    {
        $curso->delete();

        return redirect(route('cursos.index'));
    }
}
