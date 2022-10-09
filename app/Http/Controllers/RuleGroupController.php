<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRuleGroupRequest;
use App\Http\Requests\UpdateRuleGroupRequest;
use App\Models\RuleGroup;

class RuleGroupController extends Controller
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
     * @param  \App\Http\Requests\StoreRuleGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRuleGroupRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RuleGroup  $ruleGroup
     * @return \Illuminate\Http\Response
     */
    public function show(RuleGroup $ruleGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RuleGroup  $ruleGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(RuleGroup $ruleGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRuleGroupRequest  $request
     * @param  \App\Models\RuleGroup  $ruleGroup
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRuleGroupRequest $request, RuleGroup $ruleGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RuleGroup  $ruleGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(RuleGroup $ruleGroup)
    {
        //
    }
}
