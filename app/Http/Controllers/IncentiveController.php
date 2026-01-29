<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Incentive;
use App\Models\IncentiveRule;
use App\Models\IncentivePerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Database\QueryException;

class IncentiveController extends Controller
{
    public function index(Request $request)
    {
        // Try loading from `incentives` table first; if it doesn't exist, fall back to
        // `incentive_performances` table (IncentivePerformance).
        try {
            $query = Incentive::with(['lead', 'salesperson', 'incentiveRule'])->latest();

            if ($request->filled('month')) {
                $query->where('month', $request->input('month'));
            }

            $incentives = $query->paginate(25)->withQueryString();
        } catch (QueryException $e) {
            // Fallback to incentive performances if incentives table missing
            $query = IncentivePerformance::with('user')->latest();
            if ($request->filled('month')) {
                $query->where('month', $request->input('month'));
            }
            $incentives = $query->paginate(25)->withQueryString();
        }

        // Provide users who have incentive performance records
        $usersWithIncentive = User::whereHas('incentivePerformance')
            ->with('incentivePerformance')
            ->orderBy('name')
            ->get();

        return view('incentives.index', compact('incentives', 'usersWithIncentive'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'emp_code' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'month' => 'required|string|max:255',
            'target_files' => 'required|integer|min:0',
            'achieved_target' => 'required|integer|min:0',
            'percentage_achieved' => 'nullable|numeric|min:0|max:100',
            'incentive_payable' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved',
            'payout_status' => 'required|in:pending,done',
        ]);

        // Calculate percentage if not provided
        if (!isset($validated['percentage_achieved']) && $validated['target_files'] > 0) {
            $validated['percentage_achieved'] = ($validated['achieved_target'] / $validated['target_files']) * 100;
        }

        Incentive::create($validated);

        return redirect()->route('incentives.index')->with('success', 'Incentive added successfully!');
    }

    public function update(Request $request, Incentive $incentive)
    {
        $validated = $request->validate([
            'emp_code' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'month' => 'required|string|max:255',
            'target_files' => 'required|integer|min:0',
            'achieved_target' => 'required|integer|min:0',
            'percentage_achieved' => 'nullable|numeric|min:0|max:100',
            'incentive_payable' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved',
            'payout_status' => 'required|in:pending,done',
        ]);

        // Calculate percentage if not provided
        if (!isset($validated['percentage_achieved']) && $validated['target_files'] > 0) {
            $validated['percentage_achieved'] = ($validated['achieved_target'] / $validated['target_files']) * 100;
        }

        $incentive->update($validated);

        return redirect()->route('incentives.index')->with('success', 'Incentive updated successfully!');
    }

    public function destroy(Incentive $incentive)
    {
        $incentive->delete();
        return redirect()->route('incentives.index')->with('success', 'Incentive deleted successfully!');
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
            'approved_by' => Auth::id(),
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
            'payout_status' => 'done',
            'payout_date' => $validated['payout_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Incentive marked as paid!');
    }
}
