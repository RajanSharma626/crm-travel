<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\CostComponent;
use App\Models\Payment;
use App\Models\ProfitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CostComponentController extends Controller
{
    public function store(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $validated['entered_by_user_id'] = $this->getCurrentUserId();
        $costComponent = $lead->costComponents()->create($validated);
        
        // Calculate and log profit
        $this->calculateAndLogProfit($lead);
        
        return redirect()->back()->with('success', 'Cost component added successfully!');
    }

    public function update(Request $request, Lead $lead, CostComponent $costComponent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $costComponent->update($validated);
        
        // Calculate and log profit
        $this->calculateAndLogProfit($lead);
        
        return redirect()->back()->with('success', 'Cost component updated successfully!');
    }

    public function destroy(Request $request, Lead $lead, CostComponent $costComponent)
    {
        $costComponent->delete();
        
        // Calculate and log profit
        $this->calculateAndLogProfit($lead);
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cost component deleted successfully!'
            ]);
        }
        
        return redirect()->back()->with('success', 'Cost component deleted successfully!');
    }
    
    /**
     * API: Add cost component
     */
    public function addCost(Request $request, Lead $lead)
    {
        if (!$request->user()->hasAnyRole(['Admin', 'Accounts', 'Accounts Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);
        
        $validated['entered_by_user_id'] = $this->getCurrentUserId();
        $costComponent = $lead->costComponents()->create($validated);
        
        // Calculate and log profit
        $this->calculateAndLogProfit($lead);
        
        return response()->json([
            'success' => true,
            'message' => 'Cost component added successfully',
            'cost_component' => $costComponent,
        ]);
    }
    
    /**
     * Calculate and log profit for a lead
     */
    private function calculateAndLogProfit(Lead $lead)
    {
        $totalSellingPrice = $lead->selling_price ?? 0;
        $totalCost = $lead->costComponents->sum('amount');
        $profit = $totalSellingPrice - $totalCost;
        
        ProfitLog::create([
            'lead_id' => $lead->id,
            'total_selling_price' => $totalSellingPrice,
            'total_cost' => $totalCost,
            'profit' => $profit,
            'computed_at' => now(),
        ]);
    }
}
