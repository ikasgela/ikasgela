<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Period;
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

        Period::create([
            'organization_id' => request('organization_id'),
            'name' => request('name'),
            'slug' => Str::slug(request('name'))
        ]);

        return retornar();
    }

    public function show(Period $period)
    {
        abort(404);
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

        $period->update([
            'organization_id' => request('organization_id'),
            'name' => request('name'),
            'slug' => strlen((string)request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('name'))
        ]);

        return retornar();
    }

    public function destroy(Period $period)
    {
        $period->delete();

        return back();
    }
}
