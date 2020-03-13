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
        ]);

        try {
            Organization::create([
                'name' => request('name'),
                'slug' => Str::slug(request('name')),
                'registration_open' => $request->has('registration_open'),
            ]);
        } catch (\Exception $e) {
            // Slug repetido
        }

        return redirect(route('organizations.index'));
    }

    public function show(Organization $organization)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(Organization $organization)
    {
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        try {
            $organization->update([
                'name' => request('name'),
                'slug' => strlen(request('slug')) > 0
                    ? Str::slug(request('slug'))
                    : Str::slug(request('name')),
                'current_period_id' => request('current_period_id'),
                'registration_open' => $request->has('registration_open'),
            ]);
        } catch (\Exception $e) {
            // Slug repetido
        }

        return redirect(route('organizations.index'));
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect(route('organizations.index'));
    }
}
