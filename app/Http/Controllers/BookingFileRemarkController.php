<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\BookingFileRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingFileRemarkController extends Controller
{
    public function index(Request $request, Lead $lead)
    {
        $currentUser = Auth::user();
        
        // Check if user is admin
        $isAdmin = $currentUser->hasRole('Admin') || 
                   $currentUser->hasRole('Developer') || 
                   $currentUser->department === 'Admin';
        
        // Apply visibility rules
        $remarksQuery = $lead->bookingFileRemarks()->with('user');
        
        if ($isAdmin) {
            // Admins see all remarks
            $remarks = $remarksQuery->orderBy('created_at', 'desc')->get();
        } else {
            $currentDept = $currentUser->department ?? '';
            
            if ($currentDept === 'Sales') {
                // Sales users see only their own remarks
                $remarks = $remarksQuery
                    ->where('user_id', $currentUser->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Other departments see remarks made by Sales + their own remarks
                $remarks = $remarksQuery
                    ->where(function ($q) use ($currentUser) {
                        $q->whereHas('user', function ($uq) {
                            $uq->where('department', 'Sales');
                        })->orWhere('user_id', $currentUser->id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'remarks' => $remarks->map(function ($remark) {
                    return [
                        'id' => $remark->id,
                        'remark' => $remark->remark,
                        'visibility' => $remark->visibility,
                        'department' => $remark->department,
                        'follow_up_at' => $remark->follow_up_at ? $remark->follow_up_at->format('Y-m-d H:i:s') : null,
                        'created_at' => $remark->created_at?->format('d M, Y h:i A'),
                        'user' => [
                            'name' => $remark->user?->name ?? 'Unknown',
                        ],
                    ];
                }),
            ]);
        }

        return view('booking-file-remarks.index', compact('lead', 'remarks'));
    }

    public function store(Request $request, Lead $lead)
    {
        // Authorization check
        $user = Auth::user();
        $isAuthorized = false;

        // Admin/Developer
        if ($user->hasRole('Admin') || $user->hasRole('Developer') || $user->department === 'Admin') {
            $isAuthorized = true;
        }
        // Non-Sales Departments (Ops, Post Sales, Accounts, etc.) - generally allowed if they have access to the system
        elseif ($user->department !== 'Sales' && $user->role !== 'Sales') {
            $isAuthorized = true;
        }
        // Sales: Only assigned user or creator (checking string ID too just in case)
        elseif ($lead->assigned_user_id == $user->id || $lead->created_by == $user->id) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
             abort(403, 'You do not have permission to add remarks to this lead.');
        }
        $validated = $request->validate([
            'remark' => 'required|string',
            'follow_up_at' => 'nullable|date',
            'visibility' => 'nullable|in:internal,public',
            'department' => 'nullable|string',
        ]);

        // Set default visibility to public if not provided
        $validated['visibility'] = $validated['visibility'] ?? 'public';

        // Get current user
        $user = Auth::user();
        $userId = $user->id;

        // Get department from user or request
        $department = $validated['department'] ?? $user->department ?? 'Sales';
        
        // Normalize follow_up_at: allow datetime-local format or date string
        if (!empty($validated['follow_up_at'])) {
            try {
                $validated['follow_up_at'] = \Carbon\Carbon::parse($validated['follow_up_at'])->toDateTimeString();
            } catch (\Exception $e) {
                $validated['follow_up_at'] = null;
            }
        }

        $remark = $lead->bookingFileRemarks()->create([
            'user_id' => $userId,
            'department' => $department,
            'remark' => $validated['remark'],
            'follow_up_at' => $validated['follow_up_at'] ?? null,
            'visibility' => $validated['visibility'],
        ]);

        if ($request->expectsJson()) {
            $remark->load('user');
            return response()->json([
                'message' => 'Remark added successfully!',
                'remark' => [
                    'id' => $remark->id,
                    'remark' => $remark->remark,
                    'visibility' => $remark->visibility,
                    'department' => $remark->department,
                    'follow_up_at' => $remark->follow_up_at ? $remark->follow_up_at->format('Y-m-d H:i:s') : null,
                    'follow_up_date' => $remark->follow_up_at ? $remark->follow_up_at->format('d M, Y') : null,
                    'follow_up_time' => $remark->follow_up_at ? $remark->follow_up_at->format('h:i A') : null,
                    'created_at' => $remark->created_at?->format('d M, Y h:i A'),
                    'user' => [
                        'name' => $remark->user?->name ?? 'Unknown',
                    ],
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Remark added successfully!');
    }

    public function update(Request $request, Lead $lead, BookingFileRemark $bookingFileRemark)
    {
        // Check ownership or admin
        $user = Auth::user();
        if ($bookingFileRemark->user_id !== $user->id && !$user->hasRole('Admin') && !$user->hasRole('Developer') && $user->department !== 'Admin') {
            abort(403, 'You can only edit your own remarks.');
        }

        $validated = $request->validate([
            'remark' => 'required|string',
            'follow_up_at' => 'nullable|date',
            'visibility' => 'nullable|in:internal,public',
        ]);

        // Set default visibility to public if not provided
        $validated['visibility'] = $validated['visibility'] ?? 'public';

        $bookingFileRemark->update($validated);
        return redirect()->back()->with('success', 'Remark updated successfully!');
    }

    public function destroy(Lead $lead, BookingFileRemark $bookingFileRemark)
    {
        // Check ownership or admin
        $user = Auth::user();
        if ($bookingFileRemark->user_id !== $user->id && !$user->hasRole('Admin') && !$user->hasRole('Developer') && $user->department !== 'Admin') {
             abort(403, 'You can only delete your own remarks.');
        }

        $bookingFileRemark->delete();
        return redirect()->back()->with('success', 'Remark deleted successfully!');
    }
}
