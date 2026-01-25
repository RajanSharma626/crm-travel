<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use App\Models\LeadHistory;
use App\Models\LeadRemark;
use App\Models\VendorPayment;
use App\Models\BookingArrivalDeparture;
use App\Models\BookingDestination;
use App\Models\BookingAccommodation;
use App\Models\BookingItinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{

    /**
     * Check if employee is Admin or Manager (can see all leads)
     */
    private function canSeeAllLeads()
    {
        $employee = Auth::user();
        return $employee->hasRole('Admin') ||
            $employee->hasRole('Developer') ||
            $employee->department === 'Admin' ||
            $employee->hasRole('Sales Manager') ||
            $employee->hasRole('Operation Manager') ||
            $employee->hasRole('Accounts Manager') ||
            $employee->hasRole('Post Sales Manager') ||
            $employee->hasRole('Delivery Manager');
    }

    /**
     * Check if employee is from Operation, Delivery, Post Sales, or Accounts department
     * These employees should only see Booking File in view-only mode
     */
    private function isNonSalesDepartment()
    {
        $employee = Auth::user();
        $role = $employee->role ?? $employee->getRoleNameAttribute();

        if (!$role) {
            return false;
        }

        $nonSalesDepartments = [
            'Operation',
            'Operation Manager',
            'Delivery',
            'Delivery Manager',
            'Post Sales',
            'Post Sales Manager',
            'Accounts',
            'Accounts Manager',
            'Ticketing',
            'Visa',
            'Insurance'
        ];

        return in_array($role, $nonSalesDepartments);
    }

    public function index(Request $request)
    {
        $statuses = [
            'new' => 'New',
            'contacted' => 'Contacted',
            'follow_up' => 'Follow Up',
            'priority' => 'Priority',
            'booked' => 'Booked',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ];

        $filters = [
            'status' => $request->input('status'),
            'search' => $request->input('search'),
            'service_id' => $request->input('service_id'),
            'destination_id' => $request->input('destination_id'),
        ];

        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'remarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }])->orderBy('created_at', 'desc');

        // If current user is not Customer Care, show only 'booked' leads by default
        $currentUser = Auth::user();
        $isCustomerCare = $currentUser && ($currentUser->hasRole('Customer Care') || $currentUser->department === 'Customer Care');
        if (! $isCustomerCare) {
            $leadsQuery->where('status', 'booked');
        }

        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        $userRole = $currentUser->role ?? $currentUser->getRoleNameAttribute();
        $userDepartment = $currentUser->department;

        // Filter leads based on user role
        if ($isAdmin) {
            // Admin/Developer: Show all leads
        } elseif ($userRole === 'Sales' || $userDepartment === 'Sales' || $userRole === 'Sales Manager') {
            // Sales users: Show only their own assigned leads
            $userId = $this->getCurrentUserId();
            if ($userId) {
                $leadsQuery->where('assigned_user_id', $userId);
            }
        } else {
            // Other users: Use existing logic (filter by assigned user if not admin/manager)
        if (!$this->canSeeAllLeads()) {
            $userId = $this->getCurrentUserId();
            if ($userId) {
                $leadsQuery->where('assigned_user_id', $userId);
                }
            }
        }

        if (!empty($filters['status'])) {
            $leadsQuery->where('status', $filters['status']);
        }

        if (!empty($filters['service_id'])) {
            $leadsQuery->where('service_id', $filters['service_id']);
        }

        if (!empty($filters['destination_id'])) {
            $leadsQuery->where('destination_id', $filters['destination_id']);
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

        $leads = $leadsQuery->paginate(25);
        $leads->appends($request->query());

        // Add latest remark to each lead
        $leads->getCollection()->transform(function ($lead) {
            $lead->latest_remark = $lead->remarks->first();
            return $lead;
        });

        $services = Service::orderBy('name')->get();
        $destinations = Destination::orderBy('name')->get();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();

        return view('leads.index', [
            'leads' => $leads,
            'statuses' => $statuses,
            'filters' => $filters,
            'services' => $services,
            'destinations' => $destinations,
            'employees' => $employees,
        ]);
    }

    public function bookings(Request $request)
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
        ];

        // Only show booked leads
        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'bookingDestinations', 'remarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }, 'bookingFileRemarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1)->with('user');
        }])
            ->where('status', 'booked')
            ->orderBy('created_at', 'desc');

        $currentUser = Auth::user();
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('Developer') || $currentUser->department === 'Admin';
        $userRole = $currentUser->role ?? $currentUser->getRoleNameAttribute();
        $userDepartment = $currentUser->department;

        // Filter booking files based on user role
        if ($isAdmin) {
            // Admin/Developer: Show all booked leads
        } elseif ($userRole === 'Sales' || $userDepartment === 'Sales' || $userRole === 'Sales Manager') {
            // Sales users: Show only their own assigned booking files
            $userId = $this->getCurrentUserId();
            if ($userId) {
                $leadsQuery->where('assigned_user_id', $userId);
            }
        } else {
            // Other users: Use existing logic (filter by assigned user if not admin/manager)
            if (!$this->canSeeAllLeads()) {
                $userId = $this->getCurrentUserId();
                if ($userId) {
                    $leadsQuery->where('assigned_user_id', $userId);
                }
            }
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

        $leads = $leadsQuery->paginate(25);
        $leads->appends($request->query());

        // Add latest remark and booking file remark to each lead
        $leads->getCollection()->transform(function ($lead) {
            $lead->latest_remark = $lead->remarks->first();
            $lead->latest_booking_file_remark = $lead->bookingFileRemarks->first();
            return $lead;
        });

        $services = Service::orderBy('name')->get();
        $destinations = Destination::orderBy('name')->get();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();

        return view('booking.bookings', [
            'leads' => $leads,
            'filters' => $filters,
            'services' => $services,
            'destinations' => $destinations,
            'employees' => $employees,
            'stageInfo' => $stageInfo,
        ]);
    }

    public function bookingForm(Lead $lead)
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
            'travellerDocuments',
            'bookingDestinations',
            'bookingArrivalDepartures',
            'bookingAccommodations',
            'bookingItineraries',
            'vendorPayments'
        ]);

        $employees = User::whereNotNull('user_id')->orderBy('name')->get();
        $destinations = \App\Models\Destination::with('locations')->orderBy('name')->get();

        // Check if user is from non-Sales department (Operation, Delivery, Post Sales, Accounts)
        // These users should only view booking file in read-only mode
        $isViewOnly = $this->isNonSalesDepartment();

        // Determine back URL based on employee department and referrer
        $backUrl = route('bookings.index');
        $employee = Auth::user();
        $role = $employee->role ?? $employee->getRoleNameAttribute();

        // Check referrer or user department to determine back URL
        $referrer = request()->header('referer');
        if ($role && in_array($role, ['Operation', 'Operation Manager'])) {
            $backUrl = route('operations.index');
        } elseif ($role && in_array($role, ['Delivery', 'Delivery Manager'])) {
            $backUrl = route('deliveries.index');
        } elseif ($role && in_array($role, ['Post Sales', 'Post Sales Manager'])) {
            $backUrl = route('post-sales.index');
        } elseif ($role && in_array($role, ['Accounts', 'Accounts Manager'])) {
            $backUrl = route('accounts.index');
        } elseif ($referrer) {
            // Check if referrer contains specific routes
            if (str_contains($referrer, '/operations')) {
                $backUrl = route('operations.index');
            } elseif (str_contains($referrer, '/deliveries')) {
                $backUrl = route('deliveries.index');
            } elseif (str_contains($referrer, '/post-sales')) {
                $backUrl = route('post-sales.index');
            } elseif (str_contains($referrer, '/accounts')) {
                $backUrl = route('accounts.index');
            }
        }

        $vendorPayments = $lead->vendorPayments;

        // Customer payment summary (for red / yellow / green UI)
        $totalReceived = $lead->payments->where('status', 'received')->sum('amount');
        $sellingPrice = $lead->selling_price ?? 0;
        if ($sellingPrice <= 0 || $totalReceived <= 0) {
            $customerPaymentState = 'none';      // no payment received
        } elseif ($totalReceived >= $sellingPrice) {
            $customerPaymentState = 'full';      // fully received
        } else {
            $customerPaymentState = 'partial';   // partially received
        }

        // Check if employee is from Ops department
        $employee = Auth::user();
        $role = $employee->role ?? $employee->getRoleNameAttribute();
        $isOpsDept = $role && in_array($role, ['Operation', 'Operation Manager']);
        $isPostSales = $role && in_array($role, ['Post Sales', 'Post Sales Manager']);
        $isRestrictedView = $role && in_array($role, ['Ticketing', 'Visa', 'Insurance']);

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
            'stageInfo' => $stageInfo,
            'currentStage' => $currentStage,
            'customerPaymentState' => $customerPaymentState,
            'totalCustomerReceived' => $totalReceived,
            'isPostSales' => $isPostSales,
            'isRestrictedView' => $isRestrictedView,
        ]);
    }

    public function create()
    {
        $services = Service::all();
        $destinations = Destination::all();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();
        return view('leads.create', compact('services', 'destinations', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'salutation' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'primary_phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'other_phone' => 'nullable|string|max:20',
            'email' => 'required|email',
            'service_id' => 'nullable|exists:services,id',
            'destination_id' => 'nullable|exists:destinations,id',
            'address' => 'nullable|string',
            'address_line' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|max:20',
            'travel_date' => 'required|date',
            'return_date' => 'nullable|date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'children_2_5' => 'nullable|integer|min:0',
            'children_6_11' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'assigned_employee_id' => 'nullable|exists:users,id',
            'status' => 'required|in:new,contacted,follow_up,priority,booked,closed,cancelled,refunded',
        ]);

        $data = $validated;
        $data['children_2_5'] = $data['children_2_5'] ?? 0;
        $data['children_6_11'] = $data['children_6_11'] ?? 0;
        $data['children'] = ($data['children_2_5'] ?? 0) + ($data['children_6_11'] ?? 0);

        // Map employee ID to user ID
        if (!empty($data['assigned_employee_id'])) {
            $employee = User::find($data['assigned_employee_id']);
            if ($employee && $employee->user_id) {
                // Try to find user by user_id or email
                $user = User::where('email', $employee->email)
                    ->orWhere('user_id', $employee->user_id)
                    ->first();
                if ($user) {
                    $data['assigned_user_id'] = $user->id;
                }
            }
            unset($data['assigned_employee_id']);
        }
        $data['assigned_user_id'] = $data['assigned_user_id'] ?? $this->getCurrentUserId();
        $data['status'] = $data['status'] ?? 'new';
        $data['created_by'] = $this->getCurrentUserId();

        // Set booked_by and booked_on if status is booked
        if ($data['status'] === 'booked') {
            $data['booked_by'] = $this->getCurrentUserId();
            $data['booked_on'] = now();
        }

        Lead::create($data);
        return redirect()->route('leads.index')->with('success', 'Lead created successfully!');
    }

    public function show(Request $request, Lead $lead)
    {
        $lead->load([
            'service',
            'destination',
            'assignedUser',
            'histories.changedBy',
            'remarks.user',
            'payments',
            'costComponents.enteredBy',
            'operation',
            'documents.uploadedBy',
            'documents.receivedBy',
            'documents.verifiedBy',
            'delivery.assignedTo'
        ]);

        if ($request->expectsJson()) {
            $statusLabels = [
                'new' => 'New',
                'contacted' => 'Contacted',
                'follow_up' => 'Follow Up',
                'priority' => 'Priority',
                'booked' => 'Booked',
                'closed' => 'Closed',
            ];

            $statusColors = [
                'new' => 'bg-info text-white',
                'contacted' => 'bg-primary text-white',
                'follow_up' => 'bg-warning text-dark',
                'priority' => 'bg-danger text-white',
                'booked' => 'bg-success text-white',
                'closed' => 'bg-secondary text-white',
            ];

            $remarks = $lead->remarks->sortByDesc('created_at')->take(10)->values()->map(function ($remark) {
                return [
                    'id' => $remark->id,
                    'remark' => $remark->remark,
                    'visibility' => $remark->visibility,
                    'follow_up_at' => $remark->follow_up_at ? $remark->follow_up_at->format('Y-m-d H:i:s') : null,
                    'follow_up_date' => $remark->follow_up_at ? $remark->follow_up_at->format('d M, Y') : null,
                    'follow_up_time' => $remark->follow_up_at ? $remark->follow_up_at->format('h:i A') : null,
                    'created_at' => $remark->created_at?->format('d M, Y h:i A'),
                    'user' => [
                        'name' => $remark->user?->name ?? 'Unknown',
                    ],
                ];
            });

            $documents = $lead->documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'type' => $doc->type,
                    'person_number' => $doc->person_number,
                    'status' => $doc->status,
                    'notes' => $doc->notes,
                ];
            });

            // Compute next follow-up (nearest future follow_up_at)
            $nextFollowUpRemark = $lead->remarks()->whereNotNull('follow_up_at')->where('follow_up_at', '>', now())->orderBy('follow_up_at', 'asc')->first();
            $nextFollowUp = null;
            if ($nextFollowUpRemark) {
                $nextFollowUp = [
                    'id' => $nextFollowUpRemark->id,
                    'follow_up_at' => $nextFollowUpRemark->follow_up_at ? $nextFollowUpRemark->follow_up_at->format('Y-m-d H:i:s') : null,
                    'follow_up_date' => $nextFollowUpRemark->follow_up_at ? $nextFollowUpRemark->follow_up_at->format('d M, Y') : null,
                    'follow_up_time' => $nextFollowUpRemark->follow_up_at ? $nextFollowUpRemark->follow_up_at->format('h:i A') : null,
                    'remark' =>
                    strlen($nextFollowUpRemark->remark) > 120 ? substr($nextFollowUpRemark->remark, 0, 120) . '...' : $nextFollowUpRemark->remark,
                ];
            }

            return response()->json([
                'lead' => [
                    'id' => $lead->id,
                    'tsq' => $lead->tsq,
                    'customer_name' => $lead->customer_name,
                    'salutation' => $lead->salutation,
                    'first_name' => $lead->first_name,
                    'last_name' => $lead->last_name,
                    'primary_phone' => $lead->primary_phone ?? $lead->phone,
                    'secondary_phone' => $lead->secondary_phone,
                    'other_phone' => $lead->other_phone,
                    'email' => $lead->email,
                    'address' => $lead->address,
                    'address_line' => $lead->address_line,
                    'city' => $lead->city,
                    'state' => $lead->state,
                    'country' => $lead->country,
                    'pin_code' => $lead->pin_code,
                    'service' => $lead->service?->name,
                    'service_id' => $lead->service_id,
                    'destination' => $lead->destination?->name,
                    'destination_id' => $lead->destination_id,
                    'travel_date' => $lead->travel_date ? $lead->travel_date->format('d M, Y') : null,
                    'travel_date_raw' => $lead->travel_date ? $lead->travel_date->format('Y-m-d') : null,
                    'return_date' => $lead->return_date ? $lead->return_date->format('d M, Y') : null,
                    'return_date_raw' => $lead->return_date ? $lead->return_date->format('Y-m-d') : null,
                    'adults' => $lead->adults,
                    'children' => $lead->children,
                    'children_2_5' => $lead->children_2_5 ?? 0,
                    'children_6_11' => $lead->children_6_11 ?? 0,
                    'infants' => $lead->infants,
                    'assigned_user' => $lead->assignedUser?->name,
                    'assigned_user_email' => $lead->assignedUser?->email,
                    'assigned_user_id' => $lead->assigned_user_id,
                    'status' => $lead->status,
                    'status_label' => $statusLabels[$lead->status] ?? ucfirst(str_replace('_', ' ', $lead->status)),
                    'status_color' => $statusColors[$lead->status] ?? 'bg-primary text-white',
                    'created_at' => $lead->created_at?->format('d M, Y h:i A'),
                    'selling_price' => $lead->selling_price,
                    'cost_components' => $lead->costComponents->map(function ($cc) {
                        return [
                            'id' => $cc->id,
                            'name' => $cc->name,
                            'amount' => $cc->amount,
                            'entered_by' => [
                                'name' => $cc->enteredBy?->name ?? 'N/A',
                            ],
                            'created_at' => $cc->created_at?->format('Y-m-d'),
                        ];
                    }),
                    'payments' => $lead->payments->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'amount' => $p->amount,
                            'method' => $p->method,
                            'payment_date' => $p->payment_date?->format('Y-m-d'),
                            'reference' => $p->reference,
                            'status' => $p->status,
                        ];
                    }),
                    'updated_at' => $lead->updated_at?->format('d M, Y h:i A'),
                    'urls' => [
                        'show' => route('leads.show', $lead),
                        'remarks_store' => route('leads.remarks.store', $lead),
                    ],
                ],
                'remarks' => $remarks,
                'documents' => $documents,
                'next_follow_up' => $nextFollowUp,
            ]);
        }

        return view('leads.index', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $services = Service::all();
        $destinations = Destination::all();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();
        return view('leads.edit', compact('lead', 'services', 'destinations', 'employees'));
    }

    public function update(Request $request, Lead $lead)
    {
        // Check if this is a booking form submission (has booking-related fields)
        $isBookingForm = $request->has('booking_destinations') ||
            $request->has('booking_flights') ||
            $request->has('booking_surface_transports') ||
            $request->has('booking_sea_transports') ||
            $request->has('booking_accommodations') ||
            $request->has('booking_itineraries');

        if ($isBookingForm) {
            // For booking form, validate and update reassigned_to, selling_price and booking-related fields
            $validated = $request->validate([
                'reassigned_employee_id' => 'nullable|exists:users,id',
                'reassigned_to' => 'nullable|exists:users,id', // Keep for backward compatibility
                'selling_price' => 'nullable|numeric|min:0',
            ]);

            // Map employee ID to user ID if provided
            $userId = null;
            if (!empty($validated['reassigned_employee_id'])) {
                $employee = User::find($validated['reassigned_employee_id']);
                if ($employee && $employee->user_id) {
                    $user = User::where('email', $employee->email)
                        ->orWhere('user_id', $employee->user_id)
                        ->first();
                    if ($user) {
                        $userId = $user->id;
                    }
                }
            } elseif (!empty($validated['reassigned_to'])) {
                $userId = $validated['reassigned_to'];
            }

            // Update reassigned_to and selling_price if provided
            $updateData = [];
            if ($userId) {
                $updateData['reassigned_to'] = $userId;
            }
            if (isset($validated['selling_price'])) {
                $updateData['selling_price'] = $validated['selling_price'];
            }
            if (!empty($updateData)) {
                $lead->update($updateData);
            }
        } else {
            // Regular lead update with full validation
            $validated = $request->validate([
                'salutation' => 'nullable|string|max:10',
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'primary_phone' => 'required|string|max:20',
                'secondary_phone' => 'nullable|string|max:20',
                'other_phone' => 'nullable|string|max:20',
                'email' => 'required|email',
                'service_id' => 'nullable|exists:services,id',
                'destination_id' => 'nullable|exists:destinations,id',
                'address' => 'nullable|string',
                'address_line' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'pin_code' => 'nullable|string|max:20',
                'travel_date' => 'required|date',
                'return_date' => 'nullable|date',
                'adults' => 'required|integer|min:1',
                'children' => 'nullable|integer|min:0',
                'children_2_5' => 'nullable|integer|min:0',
                'children_6_11' => 'nullable|integer|min:0',
                'infants' => 'nullable|integer|min:0',
                'assigned_employee_id' => 'nullable|exists:users,id',
                'status' => 'required|in:new,contacted,follow_up,priority,booked,closed,cancelled,refunded',
                'reassigned_employee_id' => 'nullable|exists:users,id',
                'reassigned_to' => 'nullable|exists:users,id', // Keep for backward compatibility
            ]);

            $validated['children_2_5'] = $validated['children_2_5'] ?? 0;
            $validated['children_6_11'] = $validated['children_6_11'] ?? 0;
            $validated['children'] = ($validated['children_2_5'] ?? 0) + ($validated['children_6_11'] ?? 0);

            // Map employee ID to user ID for database compatibility
            if (!empty($validated['assigned_employee_id'])) {
                $assignedEmployee = User::find($validated['assigned_employee_id']);
                if ($assignedEmployee && $assignedEmployee->user_id) {
                    // Find or create user record for database compatibility
                    $user = User::where('user_id', $assignedEmployee->user_id)
                        ->orWhere('email', $assignedEmployee->email)
                        ->first();
                    if (!$user) {
                        // Create user record if it doesn't exist
                        $user = User::create([
                            'name' => $assignedEmployee->name,
                            'email' => $assignedEmployee->email ?? $assignedEmployee->user_id . '@travelshravel.com',
                            'password' => $assignedEmployee->password,
                            'user_id' => $assignedEmployee->user_id,
                            'role' => $assignedEmployee->role ?? 'Sales',
                            'status' => 'Active',
                        ]);
                    }
                    if ($user) {
                        $validated['assigned_user_id'] = $user->id;
                    }
                }
                unset($validated['assigned_employee_id']);
            }

            // Map reassigned_employee_id to reassigned_to
            if (!empty($validated['reassigned_employee_id'])) {
                $employee = User::find($validated['reassigned_employee_id']);
                if ($employee && $employee->user_id) {
                    $user = User::where('email', $employee->email)
                        ->orWhere('user_id', $employee->user_id)
                        ->first();
                    if ($user) {
                        $validated['reassigned_to'] = $user->id;
                    }
                }
                unset($validated['reassigned_employee_id']);
            }

            // Set booked_by and booked_on if status is changing to booked
            if ($validated['status'] === 'booked' && $lead->status !== 'booked') {
                $validated['booked_by'] = $this->getCurrentUserId();
                $validated['booked_on'] = now();
            }

            $lead->update($validated);
        }

        // Handle booking destinations
        if ($request->has('booking_destinations')) {
            // Delete existing booking destinations
            $lead->bookingDestinations()->delete();

            // Create new booking destinations
            foreach ($request->booking_destinations as $destData) {
                if (!empty($destData['destination']) || !empty($destData['location'])) {
                    $fromDate = !empty($destData['from_date']) ? $destData['from_date'] : null;
                    $toDate = !empty($destData['to_date']) ? $destData['to_date'] : null;
                    $noOfDays = null;

                    if ($fromDate && $toDate) {
                        $from = new \DateTime($fromDate);
                        $to = new \DateTime($toDate);
                        $diff = $from->diff($to);
                        $noOfDays = $diff->days;
                    }

                    $lead->bookingDestinations()->create([
                        'destination' => $destData['destination'] ?? null,
                        'location' => $destData['location'] ?? null,
                        'only_hotel' => isset($destData['only_hotel']) && $destData['only_hotel'] == '1',
                        'only_tt' => isset($destData['only_tt']) && $destData['only_tt'] == '1',
                        'hotel_tt' => isset($destData['hotel_tt']) && $destData['hotel_tt'] == '1',
                        'from_date' => $fromDate,
                        'to_date' => $toDate,
                        'no_of_days' => $noOfDays,
                    ]);
                }
            }
        }

        // Handle unified arrival/departure details
        if ($request->has('arrival_departure')) {
            // Delete all existing arrival/departure records
            $lead->bookingArrivalDepartures()->delete();

            // Process unified arrival/departure data
            foreach ($request->arrival_departure as $transportData) {
                $mode = $transportData['mode'] ?? '';
                $hasData = !empty($transportData['from_city']) || !empty($transportData['info']) ||
                    !empty($transportData['departure_date']) || !empty($transportData['arrival_date']);

                if (!$hasData || empty($mode)) {
                    continue;
                }

                // Create record in unified table
                $lead->bookingArrivalDepartures()->create([
                    'mode' => $mode,
                    'info' => $transportData['info'] ?? null,
                    'from_city' => $transportData['from_city'] ?? null,
                    'to_city' => $transportData['to_city'] ?? null,
                    'departure_date' => !empty($transportData['departure_date']) ? $transportData['departure_date'] : null,
                    'departure_time' => !empty($transportData['departure_time']) ? $transportData['departure_time'] : null,
                    'arrival_date' => !empty($transportData['arrival_date']) ? $transportData['arrival_date'] : null,
                    'arrival_time' => !empty($transportData['arrival_time']) ? $transportData['arrival_time'] : null,
                ]);
            }
        } else {
            // Fallback: Handle legacy separate booking sections if they exist
            // Handle booking flights
            if ($request->has('booking_flights')) {
                // Delete existing booking flights
                $lead->bookingFlights()->delete();

                // Create new booking flights
                foreach ($request->booking_flights as $flightData) {
                    if (!empty($flightData['airline']) || !empty($flightData['from_city']) || !empty($flightData['to_city'])) {
                        $lead->bookingFlights()->create([
                            'airline' => $flightData['airline'] ?? null,
                            'info' => $flightData['info'] ?? null,
                            'from_city' => $flightData['from_city'] ?? null,
                            'to_city' => $flightData['to_city'] ?? null,
                            'departure_date' => !empty($flightData['departure_date']) ? $flightData['departure_date'] : null,
                            'departure_time' => !empty($flightData['departure_time']) ? $flightData['departure_time'] : null,
                            'arrival_date' => !empty($flightData['arrival_date']) ? $flightData['arrival_date'] : null,
                            'arrival_time' => !empty($flightData['arrival_time']) ? $flightData['arrival_time'] : null,
                        ]);
                    }
                }
            }

            // Handle booking surface transports
            if ($request->has('booking_surface_transports')) {
                // Delete existing booking surface transports
                $lead->bookingSurfaceTransports()->delete();

                // Create new booking surface transports
                foreach ($request->booking_surface_transports as $transportData) {
                    if (!empty($transportData['mode']) || !empty($transportData['from_city'])) {
                        $lead->bookingSurfaceTransports()->create([
                            'mode' => $transportData['mode'] ?? null,
                            'info' => $transportData['info'] ?? null,
                            'from_city' => $transportData['from_city'] ?? null,
                            'departure_date' => !empty($transportData['departure_date']) ? $transportData['departure_date'] : null,
                            'departure_time' => !empty($transportData['departure_time']) ? $transportData['departure_time'] : null,
                            'arrival_date' => !empty($transportData['arrival_date']) ? $transportData['arrival_date'] : null,
                            'arrival_time' => !empty($transportData['arrival_time']) ? $transportData['arrival_time'] : null,
                        ]);
                    }
                }
            }

            // Handle booking sea transports
            if ($request->has('booking_sea_transports')) {
                // Delete existing booking sea transports
                $lead->bookingSeaTransports()->delete();

                // Create new booking sea transports
                foreach ($request->booking_sea_transports as $seaTransportData) {
                    if (!empty($seaTransportData['cruise']) || !empty($seaTransportData['from_city'])) {
                        $lead->bookingSeaTransports()->create([
                            'cruise' => $seaTransportData['cruise'] ?? null,
                            'info' => $seaTransportData['info'] ?? null,
                            'from_city' => $seaTransportData['from_city'] ?? null,
                            'departure_date' => !empty($seaTransportData['departure_date']) ? $seaTransportData['departure_date'] : null,
                            'departure_time' => !empty($seaTransportData['departure_time']) ? $seaTransportData['departure_time'] : null,
                            'arrival_date' => !empty($seaTransportData['arrival_date']) ? $seaTransportData['arrival_date'] : null,
                            'arrival_time' => !empty($seaTransportData['arrival_time']) ? $seaTransportData['arrival_time'] : null,
                        ]);
                    }
                }
            }
        }

        // Handle booking accommodations
        if ($request->has('booking_accommodations')) {
            // Delete existing booking accommodations
            $lead->bookingAccommodations()->delete();

            // Create new booking accommodations
            foreach ($request->booking_accommodations as $accommodationData) {
                if (!empty($accommodationData['destination']) || !empty($accommodationData['location']) || !empty($accommodationData['stay_at'])) {
                    $lead->bookingAccommodations()->create([
                        'destination' => $accommodationData['destination'] ?? null,
                        'location' => $accommodationData['location'] ?? null,
                        'stay_at' => $accommodationData['stay_at'] ?? null,
                        'checkin_date' => !empty($accommodationData['checkin_date']) ? $accommodationData['checkin_date'] : null,
                        'checkout_date' => !empty($accommodationData['checkout_date']) ? $accommodationData['checkout_date'] : null,
                        'room_type' => $accommodationData['room_type'] ?? null,
                        'meal_plan' => $accommodationData['meal_plan'] ?? null,
                        'confirmation_no' => $accommodationData['confirmation_no'] ?? null,
                        'single_room' => $accommodationData['single_room'] ?? 0,
                        'dbl_room' => $accommodationData['dbl_room'] ?? 0,
                        'quad_room' => $accommodationData['quad_room'] ?? 0,
                        'eba' => $accommodationData['eba'] ?? 0,
                        'cwb' => $accommodationData['cwb'] ?? 0,
                        'twin' => $accommodationData['twin'] ?? 0,
                        'trpl' => $accommodationData['trpl'] ?? 0,
                        'inf' => $accommodationData['inf'] ?? 0,
                        'cnb' => $accommodationData['cnb'] ?? 0,
                    ]);
                }
            }
        }

        // Handle booking itineraries
        if ($request->has('booking_itineraries')) {
            // Delete existing booking itineraries
            $lead->bookingItineraries()->delete();

            // Create new booking itineraries
            foreach ($request->booking_itineraries as $itineraryData) {
                if (!empty($itineraryData['day_and_date']) || !empty($itineraryData['location']) || !empty($itineraryData['activity_tour_description'])) {
                    $lead->bookingItineraries()->create([
                        'day_and_date' => $itineraryData['day_and_date'] ?? null,
                        'time' => !empty($itineraryData['time']) ? $itineraryData['time'] : null,
                        'service_type' => $itineraryData['service_type'] ?? null,
                        'location' => $itineraryData['location'] ?? null,
                        'activity_tour_description' => $itineraryData['activity_tour_description'] ?? null,
                        'stay_at' => $itineraryData['stay_at'] ?? null,
                        'sure' => $itineraryData['sure'] ?? null,
                        'remarks' => $itineraryData['remarks'] ?? null,
                    ]);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Lead updated successfully!',
            ]);
        }

        return redirect()->route('leads.index', $lead)->with('success', 'Lead updated successfully!');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully!');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,follow_up,priority,booked,closed,cancelled,refunded',
            'note' => 'nullable|string',
        ]);

        $oldStatus = $lead->status;
        $updateData = ['status' => $validated['status']];

        // Set booked_by and booked_on if status is changing to booked
        if ($validated['status'] === 'booked' && $oldStatus !== 'booked') {
            $updateData['booked_by'] = $this->getCurrentUserId();
            $updateData['booked_on'] = now();
        }

        $lead->update($updateData);

        // History is tracked by observer

        return redirect()->back()->with('success', 'Status updated successfully!');
    }

    public function updateAssignedUser(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'assigned_employee_id' => 'nullable|exists:users,id',
            'assigned_user_id' => 'nullable|exists:users,id', // Keep for backward compatibility
        ]);

        $userId = null;

        // If employee_id is provided, map it to user_id
        if (!empty($validated['assigned_employee_id'])) {
            $employee = User::find($validated['assigned_employee_id']);
            if ($employee && $employee->user_id) {
                $user = User::where('email', $employee->email)
                    ->orWhere('user_id', $employee->user_id)
                    ->first();
                if ($user) {
                    $userId = $user->id;
                }
            }
        } elseif (!empty($validated['assigned_user_id'])) {
            // Backward compatibility: if user_id is provided directly
            $userId = $validated['assigned_user_id'];
        }

        if (!$userId) {
            return response()->json([
                'message' => 'Invalid employee or user selected.',
            ], 422);
        }

        $oldAssignedUser = $lead->assignedUser;
        $lead->update(['assigned_user_id' => $userId]);
        $newAssignedUser = $lead->fresh()->assignedUser;

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'User assigned successfully!',
                'assigned_user' => [
                    'id' => $newAssignedUser?->id,
                    'name' => $newAssignedUser?->name ?? 'Unassigned',
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Assigned user updated successfully!');
    }

    public function updateReassignedUser(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'reassigned_employee_id' => 'nullable|exists:users,id',
            'reassigned_user_id' => 'nullable|exists:users,id',
            'assigned_employee_id' => 'nullable|exists:users,id',
            'reassigned_to' => 'nullable|exists:users,id',
            'post_sales_assignee' => 'nullable|exists:users,id',
            'operations_assignee' => 'nullable|exists:users,id',
            'ticketing_assignee' => 'nullable|exists:users,id',
            'visa_assignee' => 'nullable|exists:users,id',
            'insurance_assignee' => 'nullable|exists:users,id',
            'accounts_assignee' => 'nullable|exists:users,id',
            'delivery_assignee' => 'nullable|exists:users,id',
        ]);

        $updates = [];
        $userId = null;

        // 1. Handle Sales Assignee (Main assigned_user_id)
        // Check existing logic for employee vs user mapping if needed, otherwise use direct ID
        if (!empty($validated['reassigned_employee_id']) || !empty($validated['assigned_employee_id'])) {
            $employeeId = $validated['reassigned_employee_id'] ?? $validated['assigned_employee_id'];
            // In the view, the value is $emp->id (User id). 
            // The existing logic tried to map it, let's keep it safe or simplify if we know it's User ID.
            // Assuming $employeeId IS the User ID we want.
            $userId = $employeeId; 
            
            // Legacy mapping check (just in case the 'employee' was a different record type in past)
            $employee = User::find($employeeId);
            if ($employee && $employee->user_id) {
                // If the selected user has a 'user_id' field pointing to another user record (rare but possible in some legacy setups)
                // We'll stick to the ID passed unless there's a specific reason not to.
                // The current view passes User model ID.
            }
        } elseif (!empty($validated['reassigned_user_id'])) {
            $userId = $validated['reassigned_user_id'];
        } elseif (!empty($validated['reassigned_to'])) {
            $userId = $validated['reassigned_to'];
        }

        if ($userId) {
            $updates['assigned_user_id'] = $userId;
        }

        // 2. Handle Department Assignees
        // Helper to add to updates array if value exists
        $mapping = [
            'post_sales_assignee' => 'post_sales_user_id',
            'operations_assignee' => 'operations_user_id',
            'ticketing_assignee' => 'ticketing_user_id',
            'visa_assignee'  => 'visa_user_id',
            'insurance_assignee' => 'insurance_user_id',
            'accounts_assignee' => 'accounts_user_id',
            'delivery_assignee' => 'delivery_user_id',
        ];

        foreach ($mapping as $input => $column) {
            if ($request->has($input)) {
                // We update it even if null (to allow un-assigning)
                // But validation says nullable, so $validated[$input] is safe
                $updates[$column] = $validated[$input] ?? null;
            }
        }

        if (!empty($updates)) {
            $lead->update($updates);
        }

        return redirect()->back()->with('success', 'Lead, and department assignees updated successfully!');
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'required|exists:leads,id',
            'assigned_employee_id' => 'nullable|exists:users,id',
            'assigned_user_id' => 'nullable|exists:users,id', // Keep for backward compatibility
        ]);

        $leadIds = $validated['lead_ids'];
        $userId = null;

        // If employee_id is provided, map it to user_id
        if (!empty($validated['assigned_employee_id'])) {
            $employee = User::find($validated['assigned_employee_id']);
            if ($employee && $employee->user_id) {
                $user = User::where('email', $employee->email)
                    ->orWhere('user_id', $employee->user_id)
                    ->first();
                if ($user) {
                    $userId = $user->id;
                }
            }
        } elseif (!empty($validated['assigned_user_id'])) {
            // Backward compatibility: if user_id is provided directly
            $userId = $validated['assigned_user_id'];
        }

        if (!$userId) {
            return response()->json([
                'message' => 'Invalid employee or user selected.',
            ], 422);
        }

        // Get leads that exist and user has permission to edit
        $leads = Lead::whereIn('id', $leadIds)->get();

        if ($leads->isEmpty()) {
            return response()->json([
                'message' => 'No valid leads found to assign.',
            ], 422);
        }

        $updatedCount = 0;
        foreach ($leads as $lead) {
            // Check permission for each lead (optional - you can remove if not needed)
            // For now, we'll update all leads that were found
            $lead->update(['assigned_user_id' => $userId]);
            $updatedCount++;
        }

        $assignedUser = \App\Models\User::find($userId);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Successfully assigned {$updatedCount} lead(s) to {$assignedUser->name}.",
                'updated_count' => $updatedCount,
            ]);
        }

        return redirect()->back()->with('success', "Successfully assigned {$updatedCount} lead(s) to {$assignedUser->name}.");
    }

    // Vendor Payment CRUD (Ops only)
    public function storeVendorPayment(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'vendor_code' => 'required|string|max:255',
            'booking_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'purchase_cost' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'nullable|string|in:Pending,Paid,Cancelled',
        ]);

        $vendorPayment = VendorPayment::create([
            'lead_id' => $lead->id,
            'vendor_code' => $validated['vendor_code'],
            'booking_type' => $validated['booking_type'],
            'location' => $validated['location'],
            'purchase_cost' => $validated['purchase_cost'],
            'due_date' => $validated['due_date'],
            'status' => $validated['status'] ?? 'Pending',
            'paid_amount' => 0,
            'pending_amount' => $validated['purchase_cost'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor payment added successfully',
                'vendor_payment' => $vendorPayment,
            ]);
        }

        return redirect()->back()->with('success', 'Vendor payment added successfully');
    }

    public function updateVendorPayment(Request $request, Lead $lead, VendorPayment $vendorPayment)
    {
        // Verify vendor payment belongs to lead
        if ($vendorPayment->lead_id !== $lead->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor payment not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Vendor payment not found');
        }

        // Only Operations team (or Admin) should update via this endpoint
        if (! $request->user()->hasAnyRole(['Admin', 'Operation', 'Operation Manager'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // If vendor payment already marked Paid, only Accounts or Admin can modify
        $currentUser = $request->user();
        $isAccounts = $currentUser->department === 'Accounts' || $currentUser->hasRole('Accounts') || $currentUser->hasRole('Accounts Manager');
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->department === 'Admin';
        if ($vendorPayment->status === 'Paid' && !($isAccounts || $isAdmin)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized: only Accounts can modify paid vendor payments'], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized: only Accounts can modify paid vendor payments');
        }

        $rules = [
            'vendor_code' => 'required|string|max:255',
            'booking_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'purchase_cost' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'nullable|string|in:Pending,Cancelled,Paid',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:255',
            'ref_no' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ];

        // Restrict status for non-Accounts/Admin users if needed, but the check above (lines 1195) handles existing Paid status.
        // If we want to prevent Ops from marking as Paid:
        if (!$isAccounts && !$isAdmin) {
             $rules['status'] = 'nullable|string|in:Pending,Cancelled';
             // Ops shouldn't be updating paid_amount etc?
        }

        $validated = $request->validate($rules);

        $status = $validated['status'] ?? 'Pending';
        $paidAmount = $validated['paid_amount'] ?? $vendorPayment->paid_amount ?? 0;

        // If purchase cost changes, and we are not explicitly engaged in payment update, we might want to reset status, but let's trust the input for now or keep existing logic.
        // Existing logic: if purchase cost changes, force status to Pending (unless it's paid?)
        if ($vendorPayment->purchase_cost != $validated['purchase_cost'] && !$isAccounts && !$isAdmin) {
             $status = 'Pending';
        }

        $vendorPayment->update([
            'vendor_code' => $validated['vendor_code'],
            'booking_type' => $validated['booking_type'],
            'location' => $validated['location'],
            'purchase_cost' => $validated['purchase_cost'],
            'due_date' => $validated['due_date'],
            'status' => $status,
            'paid_amount' => $paidAmount,
            'pending_amount' => $validated['purchase_cost'] - $paidAmount,
            'payment_mode' => $validated['payment_mode'] ?? $vendorPayment->payment_mode,
            'ref_no' => $validated['ref_no'] ?? $vendorPayment->ref_no,
            'remarks' => $validated['remarks'] ?? $vendorPayment->remarks,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor payment updated successfully',
                'vendor_payment' => $vendorPayment->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Vendor payment updated successfully');
    }

    public function destroyVendorPayment(Request $request, Lead $lead, VendorPayment $vendorPayment)
    {
        // Verify vendor payment belongs to lead
        if ($vendorPayment->lead_id !== $lead->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor payment not found',
            ], 404);
        }
        // If vendor payment marked Paid, prevent deletion by non-Accounts users
        $currentUser = $request->user();
        $isAccounts = $currentUser->department === 'Accounts' || $currentUser->hasRole('Accounts') || $currentUser->hasRole('Accounts Manager');
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->department === 'Admin';
        if ($vendorPayment->status === 'Paid' && !($isAccounts || $isAdmin)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: only Accounts can delete paid vendor payments'], 403);
        }

        $vendorPayment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor payment deleted successfully',
        ]);
    }

    /**
     * Store a new arrival/departure record
     */
    public function storeBookingArrivalDeparture(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'mode' => 'required|string|max:255',
            'info' => 'nullable|string|max:255',
            'from_city' => 'required|string|max:255',
            'to_city' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'departure_time' => 'nullable|string|max:255',
            'arrival_date' => 'required|date',
            'arrival_time' => 'nullable|string|max:255',
        ]);

        $arrivalDeparture = $lead->bookingArrivalDepartures()->create([
            'mode' => $validated['mode'],
            'info' => $validated['info'] ?? null,
            'from_city' => $validated['from_city'],
            'to_city' => $validated['to_city'],
            'departure_date' => $validated['departure_date'],
            'departure_time' => $validated['departure_time'] ?? null,
            'arrival_date' => $validated['arrival_date'],
            'arrival_time' => $validated['arrival_time'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Arrival/Departure added successfully',
                'arrival_departure' => $arrivalDeparture,
            ]);
        }

        return redirect()->back()->with('success', 'Arrival/Departure added successfully');
    }

    /**
     * Update an existing arrival/departure record
     */
    public function updateBookingArrivalDeparture(Request $request, Lead $lead, BookingArrivalDeparture $arrivalDeparture)
    {
        // Verify arrival/departure belongs to lead
        if ($arrivalDeparture->lead_id !== $lead->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arrival/Departure record not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Arrival/Departure record not found');
        }

        $validated = $request->validate([
            'mode' => 'required|string|max:255',
            'info' => 'nullable|string|max:255',
            'from_city' => 'required|string|max:255',
            'to_city' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'departure_time' => 'nullable|string|max:255',
            'arrival_date' => 'required|date',
            'arrival_time' => 'nullable|string|max:255',
        ]);

        $arrivalDeparture->update([
            'mode' => $validated['mode'],
            'info' => $validated['info'] ?? null,
            'from_city' => $validated['from_city'],
            'to_city' => $validated['to_city'],
            'departure_date' => $validated['departure_date'],
            'departure_time' => $validated['departure_time'] ?? null,
            'arrival_date' => $validated['arrival_date'],
            'arrival_time' => $validated['arrival_time'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Arrival/Departure updated successfully',
                'arrival_departure' => $arrivalDeparture->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Arrival/Departure updated successfully');
    }

    /**
     * Delete an arrival/departure record
     */
    public function destroyBookingArrivalDeparture(Request $request, Lead $lead, BookingArrivalDeparture $arrivalDeparture)
    {
        // Verify arrival/departure belongs to lead
        if ($arrivalDeparture->lead_id !== $lead->id) {
            return response()->json([
                'success' => false,
                'message' => 'Arrival/Departure record not found',
            ], 404);
        }

        $arrivalDeparture->delete();

        return response()->json([
            'success' => true,
            'message' => 'Arrival/Departure deleted successfully',
        ]);
    }

    /**
     * Store a new booking destination record
     */
    public function storeBookingDestination(Request $request, Lead $lead)
    {
        // Handle both form data and JSON
        $serviceType = $request->input('service_type');
        $onlyHotel = ($serviceType === 'only_hotel') ? 1 : 0;
        $onlyTT = ($serviceType === 'only_tt') ? 1 : 0;
        $hotelTT = ($serviceType === 'hotel_tt') ? 1 : 0;

        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'service_type' => 'nullable|string|in:only_hotel,only_tt,hotel_tt',
            'only_hotel' => 'nullable|integer|in:0,1',
            'only_tt' => 'nullable|integer|in:0,1',
            'hotel_tt' => 'nullable|integer|in:0,1',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        // Use service_type if provided, otherwise use individual flags
        $finalOnlyHotel = $serviceType === 'only_hotel' ? 1 : ($validated['only_hotel'] ?? 0);
        $finalOnlyTT = $serviceType === 'only_tt' ? 1 : ($validated['only_tt'] ?? 0);
        $finalHotelTT = $serviceType === 'hotel_tt' ? 1 : ($validated['hotel_tt'] ?? 0);

        // Calculate number of days
        $fromDate = new \DateTime($validated['from_date']);
        $toDate = new \DateTime($validated['to_date']);
        $diff = $fromDate->diff($toDate);
        $noOfDays = $diff->days;

        $bookingDestination = $lead->bookingDestinations()->create([
            'destination' => $validated['destination'],
            'location' => $validated['location'],
            'only_hotel' => (bool)$finalOnlyHotel,
            'only_tt' => (bool)$finalOnlyTT,
            'hotel_tt' => (bool)$finalHotelTT,
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'no_of_days' => $noOfDays,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Destination added successfully',
                'booking_destination' => $bookingDestination,
            ]);
        }

        return redirect()->back()->with('success', 'Destination added successfully');
    }

    /**
     * Update an existing booking destination record
     */
    public function updateBookingDestination(Request $request, Lead $lead, BookingDestination $bookingDestination)
    {
        // Verify booking destination belongs to lead
        if ($bookingDestination->lead_id !== $lead->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking destination not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Booking destination not found');
        }

        // Handle both form data and JSON
        $serviceType = $request->input('service_type');
        $onlyHotel = ($serviceType === 'only_hotel') ? 1 : 0;
        $onlyTT = ($serviceType === 'only_tt') ? 1 : 0;
        $hotelTT = ($serviceType === 'hotel_tt') ? 1 : 0;

        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'service_type' => 'nullable|string|in:only_hotel,only_tt,hotel_tt',
            'only_hotel' => 'nullable|integer|in:0,1',
            'only_tt' => 'nullable|integer|in:0,1',
            'hotel_tt' => 'nullable|integer|in:0,1',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        // Use service_type if provided, otherwise use individual flags
        $finalOnlyHotel = $serviceType === 'only_hotel' ? 1 : ($validated['only_hotel'] ?? 0);
        $finalOnlyTT = $serviceType === 'only_tt' ? 1 : ($validated['only_tt'] ?? 0);
        $finalHotelTT = $serviceType === 'hotel_tt' ? 1 : ($validated['hotel_tt'] ?? 0);

        // Calculate number of days
        $fromDate = new \DateTime($validated['from_date']);
        $toDate = new \DateTime($validated['to_date']);
        $diff = $fromDate->diff($toDate);
        $noOfDays = $diff->days;

        $bookingDestination->update([
            'destination' => $validated['destination'],
            'location' => $validated['location'],
            'only_hotel' => (bool)$finalOnlyHotel,
            'only_tt' => (bool)$finalOnlyTT,
            'hotel_tt' => (bool)$finalHotelTT,
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'no_of_days' => $noOfDays,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Destination updated successfully',
                'booking_destination' => $bookingDestination->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Destination updated successfully');
    }

    /**
     * Delete a booking destination record
     */
    public function destroyBookingDestination(Request $request, Lead $lead, BookingDestination $bookingDestination)
    {
        // Verify booking destination belongs to lead
        if ($bookingDestination->lead_id !== $lead->id) {
            return response()->json([
                'success' => false,
                'message' => 'Booking destination not found',
            ], 404);
        }

        $bookingDestination->delete();

        return response()->json([
            'success' => true,
            'message' => 'Destination deleted successfully',
        ]);
    }

    /**
     * Store a new booking accommodation record
     */
    public function storeBookingAccommodation(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'destination' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'stay_at' => 'nullable|string|max:255',
            'checkin_date' => 'nullable|date',
            'checkout_date' => 'nullable|date',
            'room_type' => 'nullable|string',
            'meal_plan' => 'nullable|string|in:EP,CP,MAP,AP,AI',
            'confirmation_no' => 'nullable|string',
            'single_room' => 'nullable|integer|min:0',
            'dbl_room' => 'nullable|integer|min:0',
            'quad_room' => 'nullable|integer|min:0',
            'eba' => 'nullable|integer|min:0',
            'cwb' => 'nullable|integer|min:0',
            'twin' => 'nullable|integer|min:0',
            'trpl' => 'nullable|integer|min:0',
            'inf' => 'nullable|integer|min:0',
            'cnb' => 'nullable|integer|min:0',
        ]);



        $accommodation = $lead->bookingAccommodations()->create([
            'destination' => $validated['destination'] ?? null,
            'location' => $validated['location'] ?? null,
            'stay_at' => $validated['stay_at'] ?? null,
            'checkin_date' => $validated['checkin_date'] ?? null,
            'checkout_date' => $validated['checkout_date'] ?? null,
            'room_type' => $validated['room_type'] ?? null,
            'meal_plan' => $validated['meal_plan'] ?? null,
            'confirmation_no' => $validated['confirmation_no'] ?? null,
            'single_room' => $validated['single_room'] ?? 0,
            'dbl_room' => $validated['dbl_room'] ?? 0,
            'quad_room' => $validated['quad_room'] ?? 0,
            'eba' => $validated['eba'] ?? 0,
            'cwb' => $validated['cwb'] ?? 0,
            'inf' => $validated['inf'] ?? 0,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Accommodation added successfully',
                'accommodation' => $accommodation,
            ]);
        }

        return redirect()->back()->with('success', 'Accommodation added successfully');
    }

    /**
     * Update an existing booking accommodation record
     */
    public function updateBookingAccommodation(Request $request, Lead $lead, BookingAccommodation $accommodation)
    {
        // Verify accommodation belongs to lead
        if ($accommodation->lead_id !== $lead->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accommodation not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Accommodation not found');
        }

        $validated = $request->validate([
            'destination' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'stay_at' => 'nullable|string|max:255',
            'checkin_date' => 'nullable|date',
            'checkout_date' => 'nullable|date',
            'room_type' => 'nullable|string',
            'meal_plan' => 'nullable|string|in:EP,CP,MAP,AP,AI',
            'confirmation_no' => 'nullable|string',
            'single_room' => 'nullable|integer|min:0',
            'dbl_room' => 'nullable|integer|min:0',
            'quad_room' => 'nullable|integer|min:0',
            'eba' => 'nullable|integer|min:0',
            'cwb' => 'nullable|integer|min:0',
            'inf' => 'nullable|integer|min:0',
        ]);



        $accommodation->update([
            'destination' => $validated['destination'] ?? null,
            'location' => $validated['location'] ?? null,
            'stay_at' => $validated['stay_at'] ?? null,
            'checkin_date' => $validated['checkin_date'] ?? null,
            'checkout_date' => $validated['checkout_date'] ?? null,
            'room_type' => $validated['room_type'] ?? null,
            'meal_plan' => $validated['meal_plan'] ?? null,
            'confirmation_no' => $validated['confirmation_no'] ?? null,
            'single_room' => $validated['single_room'] ?? 0,
            'dbl_room' => $validated['dbl_room'] ?? 0,
            'quad_room' => $validated['quad_room'] ?? 0,
            'eba' => $validated['eba'] ?? 0,
            'cwb' => $validated['cwb'] ?? 0,
            'inf' => $validated['inf'] ?? 0,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Accommodation updated successfully',
                'accommodation' => $accommodation->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Accommodation updated successfully');
    }

    /**
     * Delete a booking accommodation record
     */
    public function destroyBookingAccommodation(Request $request, Lead $lead, BookingAccommodation $accommodation)
    {
        // Verify accommodation belongs to lead
        if ($accommodation->lead_id !== $lead->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accommodation not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Accommodation not found');
        }

        $accommodation->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Accommodation deleted successfully',
            ]);
        }

        return redirect()->back()->with('success', 'Accommodation deleted successfully');
    }

    /**
     * Store a new booking itinerary record
     */
    public function storeBookingItinerary(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'day_and_date' => 'nullable|string|max:255',
            'time' => 'nullable|string',
            'service_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'activity_tour_description' => 'nullable|string',
            'stay_at' => 'nullable|string|max:255',
            'sure' => 'nullable|string|in:Y,N',
            'remarks' => 'nullable|string',
        ]);

        $itinerary = $lead->bookingItineraries()->create([
            'day_and_date' => $validated['day_and_date'] ?? null,
            'time' => $validated['time'] ?? null,
            'service_type' => $validated['service_type'] ?? null,
            'location' => $validated['location'] ?? null,
            'activity_tour_description' => $validated['activity_tour_description'] ?? null,
            'stay_at' => $validated['stay_at'] ?? null,
            'sure' => $validated['sure'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Itinerary added successfully',
                'itinerary' => $itinerary,
            ]);
        }

        return redirect()->back()->with('success', 'Itinerary added successfully');
    }

    /**
     * Update an existing booking itinerary record
     */
    public function updateStages(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'stage' => 'required|string|max:255',
        ]);

        // Get user's department and determine which stage field to update
        $userDepartment = $this->getUserDepartment();
        $stageInfo = $this->getDepartmentStages($userDepartment);
        $stageKey = $stageInfo['stage_key'];

        // Validate that the stage is in the allowed list for this department
        if (!in_array($validated['stage'], $stageInfo['stages'])) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid stage for your department',
                ], 422);
            }
            return redirect()->back()->with('error', 'Invalid stage for your department');
        }

        // Update the appropriate stage field
        $lead->update([
            $stageKey => $validated['stage']
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Stage updated successfully!',
                'stage' => $validated['stage']
            ]);
        }

        return redirect()->back()->with('success', 'Stage updated successfully!');
    }

    public function updateBookingItinerary(Request $request, Lead $lead, BookingItinerary $itinerary)
    {
        // Verify itinerary belongs to lead
        if ($itinerary->lead_id !== $lead->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Itinerary not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Itinerary not found');
        }

        $validated = $request->validate([
            'day_and_date' => 'nullable|string|max:255',
            'time' => 'nullable|string',
            'service_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'activity_tour_description' => 'nullable|string',
            'stay_at' => 'nullable|string|max:255',
            'sure' => 'nullable|string|in:Y,N',
            'remarks' => 'nullable|string',
        ]);

        $itinerary->update([
            'day_and_date' => $validated['day_and_date'] ?? null,
            'time' => $validated['time'] ?? null,
            'service_type' => $validated['service_type'] ?? null,
            'location' => $validated['location'] ?? null,
            'activity_tour_description' => $validated['activity_tour_description'] ?? null,
            'stay_at' => $validated['stay_at'] ?? null,
            'sure' => $validated['sure'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Itinerary updated successfully',
                'itinerary' => $itinerary->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Itinerary updated successfully');
    }

    /**
     * Delete a booking itinerary record
     */
    public function destroyBookingItinerary(Request $request, Lead $lead, BookingItinerary $itinerary)
    {
        // Verify itinerary belongs to lead
        if ($itinerary->lead_id !== $lead->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Itinerary not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Itinerary not found');
        }

        $itinerary->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Itinerary deleted successfully',
            ]);
        }

        return redirect()->back()->with('success', 'Itinerary deleted successfully');
    }
}
