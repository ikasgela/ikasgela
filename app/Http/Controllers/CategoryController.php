<?php

namespace App\Http\Controllers;

use App\Category;
use App\Period;
use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $categories = Category::all();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $periods = Period::orderBy('name')->get();

        return view('categories.create', compact('periods'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'period_id' => 'required',
            'name' => 'required',
        ]);

        try {
            Category::create([
                'period_id' => request('period_id'),
                'name' => request('name'),
                'slug' => Str::slug(request('name'))
            ]);
        } catch (\Exception $e) {
            // Slug repetido
        }

        return retornar();
    }

    public function show(Category $category)
    {
        return abort(501, __('Not implemented.'));
    }

    public function edit(Category $category)
    {
        $periods = Period::orderBy('name')->get();

        return view('categories.edit', compact(['category', 'periods']));
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'period_id' => 'required',
            'name' => 'required',
        ]);

        try {
            $category->update([
                'period_id' => request('period_id'),
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

    public function destroy(Category $category)
    {
        $category->delete();

        return back();
    }
}
