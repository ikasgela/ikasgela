<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSelectorRequest;
use App\Http\Requests\UpdateSelectorRequest;
use App\Models\Selector;

class SelectorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSelectorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSelectorRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Selector  $selector
     * @return \Illuminate\Http\Response
     */
    public function show(Selector $selector)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Selector  $selector
     * @return \Illuminate\Http\Response
     */
    public function edit(Selector $selector)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSelectorRequest  $request
     * @param  \App\Models\Selector  $selector
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSelectorRequest $request, Selector $selector)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Selector  $selector
     * @return \Illuminate\Http\Response
     */
    public function destroy(Selector $selector)
    {
        //
    }
}
