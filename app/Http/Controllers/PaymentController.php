<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Payment;
use App\Models\CostComponent;
use App\Models\ProfitLog;
use App\Models\AccountSummary;
use App\Models\VendorPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Get stage info for current user's department
        $userDepartment = $this->getUserDepartment();
        $stageInfo = $this->getDepartmentStages($userDepartment);

        $filters = [
            'search' => $request->input('search'),
            'payment_status' => $request->input('payment_status'),
            'travel_date' => $request->input('travel_date'),
            'stage' => $request->input('stage'),
            'next_days' => $request->input('next_days'),
            'service' => $request->input('service'),
            'destination' => $request->input('destination'),
            'follow_up_date' => $request->input('follow_up_date'),
        ];

        // Show booked leads with payment and cost information for Accounts team
        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'payments', 'costComponents', 'operation', 'bookingDestinations', 'remarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }])
            ->where('status', 'booked')
            ->orderBy('created_at', 'desc');

        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        $userRole = $currentUser->role ?? $currentUser->getRoleNameAttribute();
        $userDepartment = $currentUser->department;

        // Filter booking files based on user role
        if ($isAdmin) {
            // Admin: Show all booked leads
        } elseif ($userRole === 'Accounts' || $userDepartment === 'Accounts' || $userRole === 'Accounts Manager') {
            // Accounts users: Show only their own assigned booking files
            $userId = $this->getCurrentUserId();
            if ($userId) {
                // Filter by accounts_user_id
                $leadsQuery->where('accounts_user_id', $userId);
            }
        }

        // Search filter
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

        // Filter by payment status if provided
        if (!empty($filters['payment_status'])) {
            $leadsQuery->whereHas('payments', function ($q) use ($filters) {
                $q->where('status', $filters['payment_status']);
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

        // Add latest remark to each lead
        $leads->getCollection()->transform(function ($lead) {
            $lead->latest_remark = $lead->remarks->first();
            return $lead;
        });

        // Calculate totals - use new status 'received' instead of 'paid'
        $totalRevenue = Payment::where('status', 'received')->sum('amount');
        $totalCost = CostComponent::sum('amount');
        $netProfit = $totalRevenue - $totalCost;

        $services = \App\Models\Service::orderBy('name')->get();
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('accounts.index', compact('leads', 'filters', 'totalRevenue', 'totalCost', 'netProfit', 'services', 'destinations', 'users', 'stageInfo'));
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
            'payments',
            'accountSummaries',
            'vendorPayments',
            'bookingFileRemarks.user',
            'histories.changedBy'
        ]);

        $users = \App\Models\User::orderBy('name')->get();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();
        $accountSummaries = $lead->accountSummaries;
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
        
        // Accounts department only sees customer section in read-only mode
        $isViewOnly = true;

        // Get stage info for current user's department
        $userDepartment = $this->getUserDepartment();
        $stageInfo = $this->getDepartmentStages($userDepartment);
        $currentStage = $lead->{$stageInfo['stage_key']} ?? 'Pending';

        return view('accounts.booking-file', compact('lead', 'users', 'employees', 'isViewOnly', 'accountSummaries', 'vendorPayments', 'customerPaymentState', 'totalReceived', 'stageInfo', 'currentStage'));
    }

    public function show(Lead $lead)
    {
        // Redirect to lead show page - payments section
        return redirect()->route('leads.show', $lead)->with('active_tab', 'payments');
    }

    public function store(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:Cash,UPI,NEFT,RTGS,WIB,Online,Cheque',
            'payment_date' => 'required|date',
            'due_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'status' => 'required|in:pending,received,refunded,To Be Refund,Refund Passed',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = $this->getCurrentUserId();
        $lead->payments()->create($validated);
        
        // Calculate and log profit
        $this->calculateAndLogProfit($lead);
        
        return redirect()->back()->with('success', 'Payment added successfully!');
    }
    
    /**
     * API: Dashboard metrics
     */
    public function dashboard(Request $request)
    {
        if (!$request->user()->hasAnyRole(['Admin', 'Accounts', 'Accounts Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $totalRevenue = Payment::where('status', 'received')->sum('amount');
        $totalCost = CostComponent::sum('amount');
        $netProfit = $totalRevenue - $totalCost;
        
        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'net_profit' => $netProfit,
        ]);
    }
    
    /**
     * API: Get leads with payment & cost data
     */
    public function leads(Request $request)
    {
        if (!$request->user()->hasAnyRole(['Admin', 'Accounts', 'Accounts Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'payments', 'costComponents', 'remarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }])
            ->orderBy('created_at', 'desc');
        
        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . trim($request->search) . '%';
            $leadsQuery->where(function ($query) use ($searchTerm) {
                $query->where('customer_name', 'like', $searchTerm)
                    ->orWhere('tsq', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm);
            });
        }
        
        $leads = $leadsQuery->paginate(25);
        
        // Transform leads with calculated values
        $leads->getCollection()->transform(function ($lead) {
            $totalPaid = $lead->payments->where('status', 'received')->sum('amount');
            $totalCost = $lead->costComponents->sum('amount');
            $profit = ($lead->selling_price ?? 0) - $totalCost;
            
            return [
                'id' => $lead->id,
                'tsq' => $lead->tsq,
                'customer_name' => $lead->customer_name,
                'phone' => $lead->primary_phone ?? $lead->phone,
                'total_paid' => $totalPaid,
                'total_cost' => $totalCost,
                'profit' => $profit,
                'last_remark' => $lead->remarks->first()?->remark,
                'created_at' => $lead->created_at->format('Y-m-d'),
            ];
        });
        
        return response()->json($leads);
    }

    /**
     * Accounts leads (show all leads)
     */
    public function accountsLeads(Request $request)
    {
        // Reuse LeadController@index to render the main leads listing (keeps behavior consistent)
        return app(\App\Http\Controllers\LeadController::class)->index($request);
    }
    
    /**
     * API: Add payment (with installment support)
     */
    public function addPayment(Request $request, Lead $lead)
    {
        if (!$request->user()->hasAnyRole(['Admin', 'Accounts', 'Accounts Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:Cash,UPI,NEFT,RTGS,WIB,Online,Cheque',
            'payment_date' => 'required|date',
            'due_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'status' => 'required|in:pending,received,refunded',
        ]);
        
        $validated['created_by'] = $this->getCurrentUserId();
        $payment = $lead->payments()->create($validated);
        
        // Calculate and log profit
        $this->calculateAndLogProfit($lead);
        
        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully',
            'payment' => $payment,
        ]);
    }
    
    /**
     * API: Export accounts data (Admin only)
     */
    public function export(Request $request)
    {
        if (!($request->user()->hasRole('Admin') || $request->user()->department === 'Admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $leads = Lead::with(['payments', 'costComponents'])->get();
        
        $data = $leads->map(function ($lead) {
            $totalPaid = $lead->payments->where('status', 'received')->sum('amount');
            $totalCost = $lead->costComponents->sum('amount');
            $profit = ($lead->selling_price ?? 0) - $totalCost;
            
            return [
                'TSQ' => $lead->tsq,
                'Customer Name' => $lead->customer_name,
                'Phone' => $lead->primary_phone ?? $lead->phone,
                'Total Paid' => $totalPaid,
                'Total Cost' => $totalCost,
                'Profit' => $profit,
                'Created On' => $lead->created_at->format('Y-m-d'),
            ];
        });
        
        $filename = 'accounts_export_' . date('Y-m-d_His') . '.csv';
        
        return Excel::download(new class($data) implements FromCollection, WithHeadings {
            private $data;
            
            public function __construct($data) {
                $this->data = $data;
            }
            
            public function collection() {
                return $this->data;
            }
            
            public function headings(): array {
                return ['TSQ', 'Customer Name', 'Phone', 'Total Paid', 'Total Cost', 'Profit', 'Created On'];
            }
        }, $filename);
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

    public function update(Request $request, Lead $lead, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:Cash,UPI,NEFT,RTGS,WIB,Online,Cheque',
            'payment_date' => 'required|date',
            'due_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'status' => 'required|in:pending,received,refunded,To Be Refund,Refund Passed',
            'notes' => 'nullable|string',
        ]);

        // If amount changes, set status to pending automatically
        if ($payment->amount != $validated['amount']) {
            $validated['status'] = 'pending';
        }

        $payment->update($validated);
        
        // Calculate and log profit
        $this->calculateAndLogProfit($lead);
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully!',
                'payment' => $payment
            ]);
        }
        
        return redirect()->back()->with('success', 'Payment updated successfully!');
    }

    public function destroy(Request $request, Lead $lead, Payment $payment)
    {
        $payment->delete();
        
        // Recalculate profit after deletion
        $this->calculateAndLogProfit($lead);
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully!'
            ]);
        }
        
        return redirect()->back()->with('success', 'Payment deleted successfully!');
    }

    /**
     * Store account summary
     */
    public function storeAccountSummary(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'ref_no' => 'nullable|string|max:255',
            'vendor_code' => 'nullable|string|max:255',
            'vendor_cost' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'vendor_payment_status' => 'nullable|in:Done,Pending',
            'referred_by' => 'nullable|string|max:255',
            'sales_cost' => 'nullable|numeric|min:0',
            'received_amount' => 'nullable|numeric|min:0',
            'customer_payment_status' => 'nullable|in:Received,Pending',
        ]);

        $validated['lead_id'] = $lead->id;
        if (empty($validated['ref_no'])) {
            $validated['ref_no'] = $lead->tsq;
        }

        AccountSummary::create($validated);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account summary added successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Account summary added successfully!');
    }

    /**
     * Update account summary
     */
    public function updateAccountSummary(Request $request, Lead $lead, AccountSummary $accountSummary)
    {
        $validated = $request->validate([
            'ref_no' => 'nullable|string|max:255',
            'vendor_code' => 'nullable|string|max:255',
            'vendor_cost' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'vendor_payment_status' => 'nullable|in:Done,Pending',
            'referred_by' => 'nullable|string|max:255',
            'sales_cost' => 'nullable|numeric|min:0',
            'received_amount' => 'nullable|numeric|min:0',
            'customer_payment_status' => 'nullable|in:Received,Pending',
        ]);

        $accountSummary->update($validated);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account summary updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Account summary updated successfully!');
    }

    /**
     * Delete account summary
     */
    public function destroyAccountSummary(Request $request, Lead $lead, AccountSummary $accountSummary)
    {
        $accountSummary->delete();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account summary deleted successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Account summary deleted successfully!');
    }

    /**
     * Update vendor payment (Accounts - Yellow part only)
     */
    public function updateVendorPaymentAccounts(Request $request, Lead $lead, VendorPayment $vendorPayment)
    {
        // Verify vendor payment belongs to lead
        if ($vendorPayment->lead_id !== $lead->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor payment not found',
            ], 404);
        }

        // Only Accounts team (or Admin) may update status via this endpoint
        $user = $request->user();
        if (! ($user->hasAnyRole(['Admin', 'Accounts', 'Accounts Manager']) || $user->department === 'Accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'paid_amount' => 'nullable|numeric|min:0',
            'pending_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:255',
            'ref_no' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'paid_on' => 'nullable|date',
            // Accounts may set status to Paid or Pending
            'status' => 'required|string|in:Paid,Pending',
        ]);

        // Calculate pending amount if not provided
        if (!isset($validated['pending_amount']) && isset($validated['paid_amount'])) {
            $validated['pending_amount'] = max(0, $vendorPayment->purchase_cost - $validated['paid_amount']);
        }

        $vendorPayment->update($validated);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor payment updated successfully',
                'vendor_payment' => $vendorPayment->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Vendor payment updated successfully!');
    }
}
