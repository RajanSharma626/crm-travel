<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Incentive;
use App\Models\IncentiveRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncentiveController extends Controller
{
    public function index()
    {
        $incentives = Incentive::with(['lead', 'salesperson', 'incentiveRule'])
            ->latest()
            ->paginate(25);
        return view('incentives.index', compact('incentives'));
    }

    public function calculate(Lead $lead)
    {
        // Only calculate if lead is booked
        if ($lead->status !== 'booked') {
            return redirect()->back()->with('error', 'Incentive can only be calculated for booked leads!');
        }

        $profit = $lead->profit;
        if ($profit <= 0) {
            return redirect()->back()->with('error', 'No profit available for incentive calculation!');
        }

        // Get active incentive rule
        $rule = IncentiveRule::where('active', true)->first();
        if (!$rule) {
            return redirect()->back()->with('error', 'No active incentive rule found!');
        }

        $incentiveAmount = $rule->calculateIncentive($profit);

        if ($incentiveAmount <= 0) {
            return redirect()->back()->with('error', 'Incentive amount is zero or below threshold!');
        }

        // Create incentive record
        Incentive::create([
            'lead_id' => $lead->id,
            'salesperson_id' => $lead->assigned_user_id,
            'profit_amount' => $profit,
            'incentive_amount' => $incentiveAmount,
            'incentive_rule_id' => $rule->id,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Incentive calculated and created successfully!');
    }

    public function approve(Request $request, Incentive $incentive)
    {
        if (!Auth::user() || !Auth::user()->can('approve incentives')) {
            return redirect()->back()->with('error', 'You do not have permission to approve incentives!');
        }

        $incentive->update([
            'status' => 'approved',
            'approved_by' => $this->getCurrentUserId(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Incentive approved successfully!');
    }

    public function markPaid(Request $request, Incentive $incentive)
    {
        if (!Auth::user() || !Auth::user()->can('mark incentives paid')) {
            return redirect()->back()->with('error', 'You do not have permission to mark incentives as paid!');
        }

        $validated = $request->validate([
            'payout_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $incentive->update([
            'status' => 'paid',
            'payout_date' => $validated['payout_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Incentive marked as paid!');
    }
}
