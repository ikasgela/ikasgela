<?php

namespace App\Http\Controllers;

use App\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index()
    {
        return view('documentos.index')->with('documentos', Documento::all());
    }

    public function create()
    {
        $documentos = json_decode('[
            { "title" : "Prueba1" },
            { "title" : "Prueba2", "description": [1,2,3] },
            { "title" : "Prueba3" }
        ]', true);

        foreach ($documentos as $documento) {
            Documento::create($documento);
        }

        return redirect(route('documentos.index'));
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Documento $documento)
    {
        //
    }

    public function edit(Documento $documento)
    {
        //
    }

    public function update(Request $request, Documento $documento)
    {
        //
    }

    public function destroy(Documento $documento)
    {
        //
    }
}
