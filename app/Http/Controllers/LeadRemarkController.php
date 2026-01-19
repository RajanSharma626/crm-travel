<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadRemarkController extends Controller
{
    public function store(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'remark' => 'required|string',
            'follow_up_at' => 'nullable|date',
            'visibility' => 'nullable|in:internal,public',
        ]);

        // Set default visibility to public if not provided
        $validated['visibility'] = $validated['visibility'] ?? 'public';

        // Map employee to user ID for database compatibility
        $employee = Auth::user();
        $userId = null;
        if ($employee && $employee->user_id) {
            $user = \App\Models\User::where('user_id', $employee->user_id)
                ->orWhere('email', $employee->login_work_email)
                ->first();
            $userId = $user ? $user->id : null;
        }
        
        // Normalize follow_up_at: allow datetime-local format or date string
        if (!empty($validated['follow_up_at'])) {
            try {
                $validated['follow_up_at'] = \Carbon\Carbon::parse($validated['follow_up_at'])->toDateTimeString();
            } catch (\Exception $e) {
                $validated['follow_up_at'] = null;
            }
        }

        $remark = $lead->remarks()->create($validated + ['user_id' => $userId]);

        if ($request->expectsJson()) {
            $remark->load('user');
            $employee = $remark->employee;
            return response()->json([
                'message' => 'Remark added successfully!',
                'remark' => [
                    'id' => $remark->id,
                    'remark' => $remark->remark,
                    'visibility' => $remark->visibility,
                    'follow_up_at' => $remark->follow_up_at ? $remark->follow_up_at->format('Y-m-d H:i:s') : null,
                    'follow_up_date' => $remark->follow_up_at ? $remark->follow_up_at->format('d M, Y') : null,
                    'follow_up_time' => $remark->follow_up_at ? $remark->follow_up_at->format('h:i A') : null,
                    'created_at' => $remark->created_at?->format('d M, Y h:i A'),
                    'user' => [
                        'name' => $employee?->name ?? $remark->user?->name ?? 'Unknown',
                    ],
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Remark added successfully!');
    }

    public function update(Request $request, Lead $lead, LeadRemark $remark)
    {
        $validated = $request->validate([
            'remark' => 'required|string',
            'follow_up_at' => 'nullable|date',
            'visibility' => 'nullable|in:internal,public',
        ]);

        // Set default visibility to public if not provided
        $validated['visibility'] = $validated['visibility'] ?? 'public';

        $remark->update($validated);
        return redirect()->back()->with('success', 'Remark updated successfully!');
    }

    public function destroy(Lead $lead, LeadRemark $remark)
    {
        $remark->delete();
        return redirect()->back()->with('success', 'Remark deleted successfully!');
    }
}
