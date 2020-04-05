<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Period;
use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $periods = Period::all();

        return view('periods.index', compact('periods'));
    }

    public function create()
    {
        $organizations = Organization::orderBy('name')->get();

        return view('periods.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'organization_id' => 'required',
            'name' => 'required',
        ]);

        try {
            Period::create([
                'organization_id' => request('organization_id'),
                'name' => request('name'),
                'slug' => Str::slug(request('name'))
            ]);
        } catch (\Exception $e) {
            // Slug repetido
        }

        return retornar();
    }

    public function show(Period $period)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(Period $period)
    {
        $organizations = Organization::orderBy('name')->get();

        return view('periods.edit', compact(['period', 'organizations']));
    }

    public function update(Request $request, Period $period)
    {
        $this->validate($request, [
            'organization_id' => 'required',
            'name' => 'required',
        ]);

        try {
            $period->update([
                'organization_id' => request('organization_id'),
                'name' => request('name'),
                'slug' => strlen(request('slug')) > 0
                    ? Str::slug(request('slug'))
                    : Str::slug(request('name'))
            ]);
        } catch (\Exception $e) {
            // Slug repetido
        }

        return retornar();
    }

    public function destroy(Period $period)
    {
        $period->delete();

        return back();
    }
}
