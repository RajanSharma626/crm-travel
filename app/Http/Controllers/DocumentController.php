<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
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

        // Show booked leads for Post Sales team
        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'documents', 'operation', 'bookingDestinations', 'remarks' => function ($q) {
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
            // Admin: Show all booked leads
        } elseif ($userRole === 'Post Sales' || $userDepartment === 'Post Sales' || $userRole === 'Post Sales Manager') {
            // Post Sales users: Show only their own assigned booking files
            $userId = $this->getCurrentUserId();
            if ($userId) {
                // Filter by post_sales_user_id (if using new column) or fallback logic
                $leadsQuery->where('post_sales_user_id', $userId);
            }
        }

        // Search filter - same as bookings/operations page
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

        return view('post-sales.index', compact('leads', 'filters', 'services', 'destinations', 'employees', 'stageInfo'));
    }

    /**
     * Post Sales leads (show all leads)
     */
    public function postSalesLeads(Request $request)
    {
        // Reuse LeadController@index so department leads show the same leads view
        return app(\App\Http\Controllers\LeadController::class)->index($request);
    }

    public function show(Lead $lead)
    {
        // Redirect to lead show page - documents section
        return redirect()->route('leads.show', $lead)->with('active_tab', 'documents');
    }

    public function store(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'status' => 'nullable|in:not_received,received,verified,rejected',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $documentData = [
            'uploaded_by' => $this->getCurrentUserId(),
            'type' => $validated['type'],
            'status' => $validated['status'] ?? 'not_received',
            'notes' => $validated['notes'] ?? null,
        ];

        // Note: file_path, file_name, file_size columns were removed in migration
        // File upload functionality is not available in the current schema

        // Update received_by and received_at when marking as received
        if (($validated['status'] ?? 'not_received') == 'received') {
            $documentData['received_by'] = $this->getCurrentUserId();
            $documentData['received_at'] = now();
        }

        $lead->documents()->create($documentData);

        return redirect()->back()->with('success', 'Document added successfully!');
    }

    public function update(Request $request, Lead $lead, Document $document)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'status' => 'required|in:not_received,received,verified,rejected',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $updateData = [
            'type' => $validated['type'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        // Handle file upload if provided
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $file = $request->file('file');
            $path = $file->store('documents/' . $lead->id, 'public');
            $updateData['file_path'] = $path;
            $updateData['file_name'] = $file->getClientOriginalName();
            $updateData['file_size'] = $file->getSize();
        }

        // Update received_by and received_at when marking as received
        if ($validated['status'] == 'received' && $document->status != 'received') {
            $updateData['received_by'] = $this->getCurrentUserId();
            $updateData['received_at'] = now();
        }

        // Update verified_by and verified_at when marking as verified/rejected
        if (in_array($validated['status'], ['verified', 'rejected']) && !in_array($document->status, ['verified', 'rejected'])) {
            $updateData['verified_by'] = $this->getCurrentUserId();
            $updateData['verified_at'] = now();
        }

        $document->update($updateData);
        return redirect()->back()->with('success', 'Document status updated successfully!');
    }

    public function destroy(Lead $lead, Document $document)
    {
        // Delete file if exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        return redirect()->back()->with('success', 'Document deleted successfully!');
    }

    public function download(Lead $lead, Document $document)
    {
        if (!$document->file_path || empty($document->file_path) || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File not found!');
        }

        return Storage::disk('public')->download($document->file_path);
    }

    public function bulkUpdate(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'documents' => 'nullable|array',
            'documents.*' => 'string|max:255',
        ]);

        $documentTypes = ['Aadhaar Card', 'Passport', 'Visa', 'Ticket', 'Voucher', 'Invoice', 'Insurance', 'Medical Certificate'];
        $selectedDocuments = $validated['documents'] ?? [];

        // Get person counts from lead
        $adults = $lead->adults ?? 0;
        $children25 = $lead->children_2_5 ?? 0;
        $children611 = $lead->children_6_11 ?? 0;
        $totalPersons = $adults + $children25 + $children611;

        // Process person-wise documents
        foreach ($documentTypes as $docType) {
            // Check if person-wise documents are being used (format: "DocumentType|PersonNumber")
            $personWiseSelected = array_filter($selectedDocuments, function($doc) use ($docType) {
                return strpos($doc, $docType . '|') === 0;
            });

            if (!empty($personWiseSelected) && $totalPersons > 0) {
                // Person-wise document handling
                for ($personNum = 1; $personNum <= $totalPersons; $personNum++) {
                    $docKey = $docType . '|' . $personNum;
                    $document = $lead->documents()
                        ->where('type', $docType)
                        ->where('person_number', $personNum)
                        ->first();
                    
                    if (in_array($docKey, $selectedDocuments)) {
                        // Document should be marked as received
                        if ($document) {
                            // Update existing document
                            if (!in_array($document->status, ['received', 'verified'])) {
                                $document->update([
                                    'status' => 'received',
                                    'received_by' => Auth::id(),
                                    'received_at' => now(),
                                ]);
                            }
                        } else {
                            // Create new document
                            $lead->documents()->create([
                                'uploaded_by' => $this->getCurrentUserId(),
                                'type' => $docType,
                                'person_number' => $personNum,
                                'status' => 'received',
                                'received_by' => Auth::id(),
                                'received_at' => now(),
                            ]);
                        }
                    } else {
                        // Document should be marked as not received (only if it exists)
                        if ($document && in_array($document->status, ['received', 'verified'])) {
                            $document->update([
                                'status' => 'not_received',
                            ]);
                        }
                    }
                }
            } else {
                // Fallback: General document handling (without person number) for backward compatibility
                $document = $lead->documents()
                    ->where('type', $docType)
                    ->whereNull('person_number')
                    ->first();
                
                if (in_array($docType, $selectedDocuments)) {
                    // Document should be marked as received
                    if ($document) {
                        // Update existing document
                        if (!in_array($document->status, ['received', 'verified'])) {
                            $document->update([
                                'status' => 'received',
                                'received_by' => Auth::id(),
                                'received_at' => now(),
                            ]);
                        }
                    } else {
                        // Create new document
                        $lead->documents()->create([
                            'uploaded_by' => $this->getCurrentUserId(),
                            'type' => $docType,
                            'person_number' => null,
                            'status' => 'received',
                            'received_by' => Auth::id(),
                            'received_at' => now(),
                        ]);
                    }
                } else {
                    // Document should be marked as not received (only if it exists)
                    if ($document && in_array($document->status, ['received', 'verified'])) {
                        $document->update([
                            'status' => 'not_received',
                        ]);
                    }
                }
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Documents updated successfully!'
            ]);
        }
        
        return redirect()->back()->with('success', 'Documents updated successfully!');
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
            'travellerDocuments',
            'bookingDestinations',
            'bookingArrivalDepartures',
            'bookingAccommodations',
            'bookingItineraries',
            'vendorPayments',
            'bookingFileRemarks.user',
            'histories.changedBy'
        ]);

        $employees = User::whereNotNull('user_id')->orderBy('name')->get();
        $destinations = \App\Models\Destination::with('locations')->orderBy('name')->get();
        
        // Post Sales booking file is completely view-only (except customer payments section)
        $isViewOnly = true;
        $isOpsDept = false; // Post Sales cannot edit Vendor Payments
        $isPostSales = true; // Identify Post Sales booking file
        $backUrl = route('post-sales.index');
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
            'isPostSales' => $isPostSales,
            'customerPaymentState' => $customerPaymentState,
            'totalCustomerReceived' => $totalReceived,
            'stageInfo' => $stageInfo,
            'currentStage' => $currentStage,
        ]);
    }
}
