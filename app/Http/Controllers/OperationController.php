<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        // Get stage info for current user's department
        $userDepartment = $this->getUserDepartment();
        $stageInfo = $this->getDepartmentStages($userDepartment);

        $filters = [
            'search' => $request->input('search'),
            'travel_date' => $request->input('travel_date'),
            'stage' => $request->input('stage'),
            'next_days' => $request->input('next_days'),
            'service' => $request->input('service'),
            'destination' => $request->input('destination'),
            'follow_up_date' => $request->input('follow_up_date'),
        ];

        // Show booked leads for Operations team
        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'operation', 'bookingDestinations', 'remarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }, 'bookingFileRemarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1)->with('user');
        }])
            ->where('status', 'booked')
            ->orderBy('created_at', 'desc');

        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        
        // Filter booking files based on user role
        if ($isAdmin) {
            // Admin: Show all booked leads
        } else {
            // Operation users: Show only their own assigned booking files
            $userId = $this->getCurrentUserId();
            if ($userId) {
                // Filter by operations_user_id OR fallback to assigned_user_id if strict ownership is not required or migrated yet
                $leadsQuery->where(function($q) use ($userId) {
                    $q->where('operations_user_id', $userId);
                });
            }
        }

        // Search filter - same as bookings page
        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            $likeTerm = '%' . $searchTerm . '%';

            $leadsQuery->where(function ($query) use ($likeTerm) {
                $query->where('customer_name', 'like', $likeTerm)
                    ->orWhere('first_name', 'like', $likeTerm)
                    ->orWhere('last_name', 'like', $likeTerm)
                    ->orWhere('phone', 'like', $likeTerm)
                    ->orWhere('primary_phone', 'like', $likeTerm)
                    ->orWhere('secondary_phone', 'like', $likeTerm)
                    ->orWhere('other_phone', 'like', $likeTerm)
                    ->orWhere('tsq', 'like', $likeTerm)
                    ->orWhere('tsq_number', 'like', $likeTerm);
            });
        }

        if (!empty($filters['travel_date'])) {
            $leadsQuery->whereDate('travel_date', '=', $filters['travel_date']);
        }

        if (!empty($filters['stage'])) {
            $leadsQuery->where($stageInfo['stage_key'], $filters['stage']);
        }

        if (!empty($filters['next_days'])) {
            $days = (int) $filters['next_days'];
            $startDate = now()->startOfDay();
            $endDate = now()->addDays($days)->endOfDay();
            $leadsQuery->whereBetween('travel_date', [$startDate, $endDate]);
        }

        if (!empty($filters['service'])) {
            $leadsQuery->where('service_id', $filters['service']);
        }

        if (!empty($filters['destination'])) {
            $leadsQuery->where('destination_id', $filters['destination']);
        }

        // Follow-up date filter - filter by user's own remarks
        if (!empty($filters['follow_up_date'])) {
            $userId = $currentUser->id;
            $leadsQuery->whereHas('bookingFileRemarks', function ($q) use ($userId, $filters) {
                $q->where('user_id', $userId)
                  ->whereDate('follow_up_at', '=', $filters['follow_up_date']);
            });
        }

        $leads = $leadsQuery->paginate(25);
        $leads->appends($request->query());

        // Add latest remark and booking file remark to each lead
        $leads->getCollection()->transform(function ($lead) {
            $lead->latest_remark = $lead->remarks->first();
            $lead->latest_booking_file_remark = $lead->bookingFileRemarks->first();
            return $lead;
        });

        $services = \App\Models\Service::orderBy('name')->get();
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();

        $indexRoute = 'operations.index';
        $bookingFileRoute = 'operations.booking-file';

        return view('operations.index', compact('leads', 'filters', 'services', 'destinations', 'employees', 'indexRoute', 'bookingFileRoute', 'stageInfo'));
    }

    /**
     * Operations leads (unique operations leads URL)
     */
    public function operationsLeads(Request $request)
    {
        // Reuse the global leads listing for consistent behavior
        return app(\App\Http\Controllers\LeadController::class)->index($request);
    }

    public function store(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'operation_status' => 'required|in:pending,in_progress,completed,cancelled',
            'nett_cost' => 'nullable|numeric|min:0',
            'internal_notes' => 'nullable|string',
        ]);

        $operation = $lead->operation()->create($validated);

        // Check if nett cost causes loss
        if ($validated['nett_cost'] && $lead->selling_price) {
            $profit = $lead->selling_price - $validated['nett_cost'];
            if ($profit < 0) {
                $operation->update([
                    'admin_approval_required' => true,
                    'approval_reason' => 'Nett cost exceeds selling price. Profit: ' . number_format($profit, 2),
                    'approval_requested_by' => $this->getCurrentUserId(),
                    'approval_requested_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Operation record created successfully!');
    }

    public function update(Request $request, Lead $lead, Operation $operation)
    {
        $validated = $request->validate([
            'operation_status' => 'required|in:pending,in_progress,completed,cancelled',
            'nett_cost' => 'nullable|numeric|min:0',
            'internal_notes' => 'nullable|string',
        ]);

        $oldNettCost = $operation->nett_cost;
        $newNettCost = $validated['nett_cost'] ?? $oldNettCost;

        // Check if nett cost change causes loss
        if ($newNettCost && $lead->selling_price && $newNettCost != $oldNettCost) {
            $profit = $lead->selling_price - $newNettCost;
            if ($profit < 0) {
                $validated['admin_approval_required'] = true;
                $validated['approval_reason'] = 'Nett cost exceeds selling price. Profit: ' . number_format($profit, 2);
                $validated['approval_requested_by'] = $this->getCurrentUserId();
                $validated['approval_requested_at'] = now();
            } else {
                $validated['admin_approval_required'] = false;
                $validated['approval_reason'] = null;
            }
        }

        $operation->update($validated);
        return redirect()->back()->with('success', 'Operation updated successfully!');
    }

    public function approve(Request $request, Lead $lead, Operation $operation)
    {
        if (!Auth::user() || !Auth::user()->can('approve operations')) {
            return redirect()->back()->with('error', 'You do not have permission to approve operations!');
        }

        $operation->update([
            'admin_approval_required' => false,
            'approval_approved_by' => $this->getCurrentUserId(),
            'approval_approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Operation approved successfully!');
    }

    public function reject(Request $request, Lead $lead, Operation $operation)
    {
        if (!Auth::user() || !Auth::user()->can('approve operations')) {
            return redirect()->back()->with('error', 'You do not have permission to reject operations!');
        }

        $operation->update([
            'admin_approval_required' => false,
            'approval_reason' => 'Rejected: ' . ($request->rejection_reason ?? 'No reason provided'),
        ]);

        return redirect()->back()->with('success', 'Operation rejected!');
    }

    public function bookingFile(Lead $lead)
    {
        $lead->load([
            'service',
            'destination',
            'assignedUser',
            'createdBy',
            'bookedBy',
            'reassignedTo',
            'costComponents',
            'payments',
            'bookingDestinations',
            'bookingArrivalDepartures',
            'bookingAccommodations',
            'bookingItineraries',
            'vendorPayments',
            'operation',
            'bookingFileRemarks.user',
            'histories.changedBy'
        ]);

        $employees = User::whereNotNull('user_id')->orderBy('name')->get();
        $destinations = \App\Models\Destination::with('locations')->orderBy('name')->get();
        
        // Ops booking file is now editable - Operations can add/edit booking file
        $isViewOnly = false;
        $isOpsDept = true; // This is Ops booking file
        $backUrl = route('operations.index');
        $vendorPayments = $lead->vendorPayments;

        // Customer payment summary (for red / yellow / green UI)
        $totalReceived = $lead->payments->where('status', 'received')->sum('amount');
        $sellingPrice = $lead->selling_price ?? 0;
        if ($sellingPrice <= 0 || $totalReceived <= 0) {
            $customerPaymentState = 'none';
        } elseif ($totalReceived >= $sellingPrice) {
            $customerPaymentState = 'full';
        } else {
            $customerPaymentState = 'partial';
        }

        // Get stage info for current user's department
        $userDepartment = $this->getUserDepartment();
        $stageInfo = $this->getDepartmentStages($userDepartment);
        $currentStage = $lead->{$stageInfo['stage_key']} ?? 'Pending';

        return view('booking.booking-form', [
            'lead' => $lead,
            'employees' => $employees,
            'destinations' => $destinations,
            'isViewOnly' => $isViewOnly,
            'backUrl' => $backUrl,
            'vendorPayments' => $vendorPayments,
            'isOpsDept' => $isOpsDept,
            'customerPaymentState' => $customerPaymentState,
            'totalCustomerReceived' => $totalReceived,
            'stageInfo' => $stageInfo,
            'currentStage' => $currentStage,
        ]);
    }

    /**
     * Ticketing index - separate tab and booking file
     */
    public function ticketingIndex(Request $request)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        $userDepartment = $currentUser->department;

        // Only Admin or Ticketing users may access
        if (! $isAdmin && ! in_array($userDepartment, ['Ticketing'])) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // reused logic
        $stageInfo = $this->getDepartmentStages('Ticketing');

        // Reuse index logic but restrict to Ticketing assignments
        $filters = [
            'search' => $request->input('search'),
            'travel_date' => $request->input('travel_date'),
            'stage' => $request->input('stage'),
            'next_days' => $request->input('next_days'),
            'service' => $request->input('service'),
            'destination' => $request->input('destination'),
        ];
        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'operation', 'bookingDestinations', 'remarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }, 'bookingFileRemarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1)->with('user');
        }])
            ->where('status', 'booked')
            ->orderBy('created_at', 'desc');

        if ($isAdmin) {
             // Admin: Show all booked leads
        } else {
            $leadsQuery->where('ticketing_user_id', $this->getCurrentUserId());
        }

        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            $likeTerm = '%' . $searchTerm . '%';
            $leadsQuery->where(function ($query) use ($likeTerm) {
                $query->where('customer_name', 'like', $likeTerm)
                    ->orWhere('first_name', 'like', $likeTerm)
                    ->orWhere('last_name', 'like', $likeTerm)
                    ->orWhere('phone', 'like', $likeTerm)
                    ->orWhere('primary_phone', 'like', $likeTerm)
                    ->orWhere('secondary_phone', 'like', $likeTerm)
                    ->orWhere('other_phone', 'like', $likeTerm)
                    ->orWhere('tsq', 'like', $likeTerm)
                    ->orWhere('tsq_number', 'like', $likeTerm);
            });
        }

        if (!empty($filters['travel_date'])) {
            $leadsQuery->whereDate('travel_date', '=', $filters['travel_date']);
        }

        if (!empty($filters['stage'])) {
            $leadsQuery->where('ticketing_stage', $filters['stage']);
        }

        if (!empty($filters['next_days'])) {
            $days = (int) $filters['next_days'];
            $startDate = now()->startOfDay();
            $endDate = now()->addDays($days)->endOfDay();
            $leadsQuery->whereBetween('travel_date', [$startDate, $endDate]);
        }

        if (!empty($filters['service'])) {
            $leadsQuery->where('service_id', $filters['service']);
        }

        if (!empty($filters['destination'])) {
            $leadsQuery->where('destination_id', $filters['destination']);
        }

        $leads = $leadsQuery->paginate(25);
        $leads->appends($request->query());

        $leads->getCollection()->transform(function ($lead) {
            $lead->latest_remark = $lead->remarks->first();
            $lead->latest_booking_file_remark = $lead->bookingFileRemarks->first();
            return $lead;
        });

        $services = \App\Models\Service::orderBy('name')->get();
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();

        $indexRoute = 'ticketing.index';
        $bookingFileRoute = 'ticketing.booking-file';

        return view('operations.index', compact('leads', 'filters', 'services', 'destinations', 'employees', 'indexRoute', 'bookingFileRoute', 'stageInfo'));
    }

    /**
     * Ticketing leads (show all leads to viewers)
     */
    public function ticketingLeads(Request $request)
    {
        return app(\App\Http\Controllers\LeadController::class)->index($request);
    }

    public function ticketingBookingFile(Lead $lead)
    {
        // allow Admin or Ticketing users
        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        $userDepartment = $currentUser->department;
        if (! $isAdmin && $userDepartment !== 'Ticketing') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $data = $this->bookingFileCommonData($lead, route('ticketing.index'), true);
        return view('booking.booking-form', $data);
    }

    /**
     * Visa index & booking file
     */
    public function visaIndex(Request $request)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        $userDepartment = $currentUser->department;

        if (! $isAdmin && $userDepartment !== 'Visa') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $stageInfo = $this->getDepartmentStages('Visa');

        $filters = [
            'search' => $request->input('search'),
            'travel_date' => $request->input('travel_date'),
            'stage' => $request->input('stage'),
            'next_days' => $request->input('next_days'),
            'service' => $request->input('service'),
            'destination' => $request->input('destination'),
        ];
        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'operation', 'bookingDestinations', 'remarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }, 'bookingFileRemarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1)->with('user');
        }])
            ->where('status', 'booked')
            ->orderBy('created_at', 'desc');

        if ($isAdmin) {
             // Admin: Show all booked leads
        } else {
            $leadsQuery->where('visa_user_id', $this->getCurrentUserId());
        }

        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            $likeTerm = '%' . $searchTerm . '%';
            $leadsQuery->where(function ($query) use ($likeTerm) {
                $query->where('customer_name', 'like', $likeTerm)
                    ->orWhere('first_name', 'like', $likeTerm)
                    ->orWhere('last_name', 'like', $likeTerm)
                    ->orWhere('phone', 'like', $likeTerm)
                    ->orWhere('primary_phone', 'like', $likeTerm)
                    ->orWhere('secondary_phone', 'like', $likeTerm)
                    ->orWhere('other_phone', 'like', $likeTerm)
                    ->orWhere('tsq', 'like', $likeTerm)
                    ->orWhere('tsq_number', 'like', $likeTerm);
            });
        }

        if (!empty($filters['travel_date'])) {
            $leadsQuery->whereDate('travel_date', '=', $filters['travel_date']);
        }

        if (!empty($filters['stage'])) {
            $leadsQuery->where('visa_stage', $filters['stage']);
        }

        if (!empty($filters['next_days'])) {
            $days = (int) $filters['next_days'];
            $startDate = now()->startOfDay();
            $endDate = now()->addDays($days)->endOfDay();
            $leadsQuery->whereBetween('travel_date', [$startDate, $endDate]);
        }

        if (!empty($filters['service'])) {
            $leadsQuery->where('service_id', $filters['service']);
        }

        if (!empty($filters['destination'])) {
            $leadsQuery->where('destination_id', $filters['destination']);
        }

        $leads = $leadsQuery->paginate(25);
        $leads->appends($request->query());

        $leads->getCollection()->transform(function ($lead) {
            $lead->latest_remark = $lead->remarks->first();
            $lead->latest_booking_file_remark = $lead->bookingFileRemarks->first();
            return $lead;
        });

        $services = \App\Models\Service::orderBy('name')->get();
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();

        $indexRoute = 'visa.index';
        $bookingFileRoute = 'visa.booking-file';

        return view('operations.index', compact('leads', 'filters', 'services', 'destinations', 'employees', 'indexRoute', 'bookingFileRoute', 'stageInfo'));
    }

    /**
     * Visa leads (show all leads)
     */
    public function visaLeads(Request $request)
    {
        // reuse ticketingLeads logic
        return $this->ticketingLeads($request);
    }

    public function visaBookingFile(Lead $lead)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        $userDepartment = $currentUser->department;
        if (! $isAdmin && $userDepartment !== 'Visa') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $data = $this->bookingFileCommonData($lead, route('visa.index'), true);
        return view('booking.booking-form', $data);
    }

    /**
     * Insurance index & booking file
     */
    public function insuranceIndex(Request $request)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        $userDepartment = $currentUser->department;

        if (! $isAdmin && $userDepartment !== 'Insurance') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $stageInfo = $this->getDepartmentStages('Insurance');

        $filters = [
            'search' => $request->input('search'),
            'travel_date' => $request->input('travel_date'),
            'stage' => $request->input('stage'),
            'next_days' => $request->input('next_days'),
            'service' => $request->input('service'),
            'destination' => $request->input('destination'),
        ];
        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'operation', 'bookingDestinations', 'remarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }, 'bookingFileRemarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1)->with('user');
        }])
            ->where('status', 'booked')
            ->orderBy('created_at', 'desc');

        if ($isAdmin) {
             // Admin: Show all booked leads
        } else {
            $leadsQuery->where('insurance_user_id', $this->getCurrentUserId());
        }

        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            $likeTerm = '%' . $searchTerm . '%';
            $leadsQuery->where(function ($query) use ($likeTerm) {
                $query->where('customer_name', 'like', $likeTerm)
                    ->orWhere('first_name', 'like', $likeTerm)
                    ->orWhere('last_name', 'like', $likeTerm)
                    ->orWhere('phone', 'like', $likeTerm)
                    ->orWhere('primary_phone', 'like', $likeTerm)
                    ->orWhere('secondary_phone', 'like', $likeTerm)
                    ->orWhere('other_phone', 'like', $likeTerm)
                    ->orWhere('tsq', 'like', $likeTerm)
                    ->orWhere('tsq_number', 'like', $likeTerm);
            });
        }

        if (!empty($filters['travel_date'])) {
            $leadsQuery->whereDate('travel_date', '=', $filters['travel_date']);
        }

        if (!empty($filters['stage'])) {
            $leadsQuery->where('insurance_stage', $filters['stage']);
        }

        if (!empty($filters['next_days'])) {
            $days = (int) $filters['next_days'];
            $startDate = now()->startOfDay();
            $endDate = now()->addDays($days)->endOfDay();
            $leadsQuery->whereBetween('travel_date', [$startDate, $endDate]);
        }
        
        if (!empty($filters['service'])) {
            $leadsQuery->where('service_id', $filters['service']);
        }

        if (!empty($filters['destination'])) {
            $leadsQuery->where('destination_id', $filters['destination']);
        }

        $leads = $leadsQuery->paginate(25);
        $leads->appends($request->query());

        $leads->getCollection()->transform(function ($lead) {
            $lead->latest_remark = $lead->remarks->first();
            $lead->latest_booking_file_remark = $lead->bookingFileRemarks->first();
            return $lead;
        });

        $services = \App\Models\Service::orderBy('name')->get();
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();

        $indexRoute = 'insurance.index';
        $bookingFileRoute = 'insurance.booking-file';

        return view('operations.index', compact('leads', 'filters', 'services', 'destinations', 'employees', 'indexRoute', 'bookingFileRoute', 'stageInfo'));
    }

    /**
     * Insurance leads (show all leads)
     */
    public function insuranceLeads(Request $request)
    {
        return $this->ticketingLeads($request);
    }

    public function insuranceBookingFile(Lead $lead)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        $userDepartment = $currentUser->department;
        if (! $isAdmin && $userDepartment !== 'Insurance') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $data = $this->bookingFileCommonData($lead, route('insurance.index'), true);
        return view('booking.booking-form', $data);
    }

    /**
     * Helper to assemble booking file data for department-specific booking files.
     */
    protected function bookingFileCommonData(Lead $lead, $backUrl, $isDeptBooking = false)
    {
        $lead->load([
            'service',
            'destination',
            'assignedUser',
            'createdBy',
            'bookedBy',
            'reassignedTo',
            'costComponents',
            'payments',
            'bookingDestinations',
            'bookingArrivalDepartures',
            'bookingAccommodations',
            'bookingItineraries',
            'vendorPayments',
            'operation',
            'bookingFileRemarks.user',
            'histories.changedBy'
        ]);

        $employees = User::whereNotNull('user_id')->orderBy('name')->get();
        $destinations = \App\Models\Destination::with('locations')->orderBy('name')->get();
        
        $isViewOnly = true;
        $isOpsDept = $isDeptBooking ? true : false;

        $vendorPayments = $lead->vendorPayments;

        $totalReceived = $lead->payments->where('status', 'received')->sum('amount');
        $sellingPrice = $lead->selling_price ?? 0;
        if ($sellingPrice <= 0 || $totalReceived <= 0) {
            $customerPaymentState = 'none';
        } elseif ($totalReceived >= $sellingPrice) {
            $customerPaymentState = 'full';
        } else {
            $customerPaymentState = 'partial';
        }

        $userDepartment = $this->getUserDepartment();
        $stageInfo = $this->getDepartmentStages($userDepartment);
        $currentStage = $lead->{$stageInfo['stage_key']} ?? 'Pending';

        return [
            'lead' => $lead,
            'employees' => $employees,
            'destinations' => $destinations,
            'isViewOnly' => $isViewOnly,
            'backUrl' => $backUrl,
            'vendorPayments' => $vendorPayments,
            'isOpsDept' => $isOpsDept,
            'customerPaymentState' => $customerPaymentState,
            'totalCustomerReceived' => $totalReceived,
            'stageInfo' => $stageInfo,
            'currentStage' => $currentStage,
        ];
    }
}
