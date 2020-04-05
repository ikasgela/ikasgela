<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);

        Role::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return retornar();
    }

    public function show(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);

        $role->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return retornar();
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return back();
    }
}
