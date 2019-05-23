<?php

namespace App\Http\Controllers;

use App\MarkdownText;
use BadMethodCallException;
use Illuminate\Http\Request;

class MarkdownTextController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor');
    }

    public function index()
    {
        $markdown_texts = MarkdownText::all();

        return view('markdown_texts.index', compact('markdown_texts'));
    }

    public function create()
    {
        return view('markdown_texts.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'repositorio' => 'required',
            'archivo' => 'required',
        ]);

        MarkdownText::create($request->all());

        return redirect(route('markdown_texts.index'));
    }

    public function show(MarkdownText $markdown_text)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(MarkdownText $markdown_text)
    {
        return view('markdown_texts.edit', compact('markdown_text'));
    }

    public function update(Request $request, MarkdownText $markdown_text)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'repositorio' => 'required',
            'archivo' => 'required',
        ]);

        $markdown_text->update($request->all());

        return redirect(route('markdown_texts.index'));
    }

    public function destroy(MarkdownText $markdown_text)
    {
        $markdown_text->delete();

        return redirect(route('markdown_texts.index'));
    }
}
