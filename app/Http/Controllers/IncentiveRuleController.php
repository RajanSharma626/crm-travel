<?php

namespace App\Http\Controllers;

use App\Models\IncentiveRule;
use Illuminate\Http\Request;

class IncentiveRuleController extends Controller
{
    public function index()
    {
        $rules = IncentiveRule::latest()->paginate(25);
        return view('incentive-rules.index', compact('rules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rule_type' => 'required|in:fixed_percentage,tiered_percentage,fixed_amount',
            'fixed_percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'min_profit_threshold' => 'nullable|numeric|min:0',
            'params' => 'nullable|json',
            'active' => 'boolean',
        ]);

        IncentiveRule::create($validated + ['active' => $request->has('active')]);
        return redirect()->route('incentive-rules.index')->with('success', 'Incentive rule created successfully!');
    }

    public function update(Request $request, IncentiveRule $incentiveRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rule_type' => 'required|in:fixed_percentage,tiered_percentage,fixed_amount',
            'fixed_percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'min_profit_threshold' => 'nullable|numeric|min:0',
            'params' => 'nullable|json',
            'active' => 'boolean',
        ]);

        $incentiveRule->update($validated + ['active' => $request->has('active')]);
        return redirect()->route('incentive-rules.index')->with('success', 'Incentive rule updated successfully!');
    }

    public function destroy(IncentiveRule $incentiveRule)
    {
        $incentiveRule->delete();
        return redirect()->route('incentive-rules.index')->with('success', 'Incentive rule deleted successfully!');
    }
}
