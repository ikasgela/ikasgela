<?php

namespace App\Http\Controllers;

use App\Qualification;
use BadMethodCallException;
use Illuminate\Http\Request;

class QualificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $qualifications = Qualification::all();

        return view('qualifications.index', compact('qualifications'));
    }

    public function create()
    {
        return view('qualifications.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        Qualification::create($request->all());

        return redirect(route('qualifications.index'));
    }

    public function show(Qualification $qualification)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(Qualification $qualification)
    {
        return view('qualifications.edit', compact('qualification'));
    }

    public function update(Request $request, Qualification $qualification)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $qualification->update($request->all());

        return redirect(route('qualifications.index'));
    }

    public function destroy(Qualification $qualification)
    {
        $qualification->delete();

        return redirect(route('qualifications.index'));
    }
}
