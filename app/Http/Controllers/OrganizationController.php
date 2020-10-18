<?php

namespace App\Http\Controllers;

use App\Organization;
use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $organizations = Organization::all();

        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'seats' => 'required',
        ]);

        Organization::create([
            'name' => request('name'),
            'slug' => Str::slug(request('name')),
            'registration_open' => $request->has('registration_open'),
            'seats' => request('seats'),
        ]);

        return retornar();
    }

    public function show(Organization $organization)
    {
        return abort(501);
    }

    public function edit(Organization $organization)
    {
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $this->validate($request, [
            'name' => 'required',
            'seats' => 'required',
        ]);

        $organization->update([
            'name' => request('name'),
            'slug' => strlen(request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('name')),
            'current_period_id' => request('current_period_id'),
            'registration_open' => $request->has('registration_open'),
            'seats' => request('seats'),
        ]);

        return retornar();
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return back();
    }
}
