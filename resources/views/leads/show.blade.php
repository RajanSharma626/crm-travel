@extends('layouts.app')
@section('title', 'Lead Details | Travel Shravel')
@section('content')
<div class="hk-pg-wrapper pb-0">
    <div class="hk-pg-body py-0">
        <div class="contactapp-wrap">
            <div class="contactapp-content">
                <div class="contactapp-detail-wrap">
                    <header class="contact-header">
                        <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                            <div>
                                <h1>{{ $lead->tsq }} - {{ $lead->customer_name }}</h1>
                                <small class="text-muted">Created: {{ $lead->created_at->format('d M, Y h:i A') }}</small>
                            </div>
                            <div>
                                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
                                <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-primary btn-sm">Edit Lead</a>
                            </div>
                        </div>
                    </header>

                    <div class="contact-body">
                        <div data-simplebar class="nicescroll-bar">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <!-- Lead Information -->
                            <div class="card mb-4 shadow-sm">
                                <div class="card-header bg-white border-bottom">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="user" class="me-2" style="width: 20px; height: 20px;"></i>
                                        <h5 class="mb-0 fw-semibold">Lead Information</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <!-- Left Column - Contact Details -->
                                        <div class="col-md-6">
                                            <div class="mb-3 pb-3 border-bottom">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i data-feather="hash" class="text-muted me-2" style="width: 16px; height: 16px;"></i>
                                                    <small class="text-muted text-uppercase fw-semibold">Lead ID</small>
                                                </div>
                                                <h6 class="mb-0 fw-bold">{{ $lead->tsq }}</h6>
                                            </div>

                                            <div class="mb-3 pb-3 border-bottom">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i data-feather="user" class="text-primary me-2" style="width: 16px; height: 16px;"></i>
                                                    <small class="text-muted text-uppercase fw-semibold">Customer Details</small>
                                                </div>
                                                <h6 class="mb-2 fw-semibold">
                                                    @if($lead->salutation)
                                                        {{ $lead->salutation }} 
                                                    @endif
                                                    {{ $lead->customer_name }}
                                                </h6>
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="d-flex align-items-center">
                                                        <i data-feather="phone" class="text-muted me-2" style="width: 14px; height: 14px;"></i>
                                                        <span class="text-muted">{{ $lead->primary_phone ?? $lead->phone }}</span>
                                                    </div>
                                                    @if($lead->secondary_phone)
                                                    <div class="d-flex align-items-center">
                                                        <i data-feather="phone-forwarded" class="text-muted me-2" style="width: 14px; height: 14px;"></i>
                                                        <span class="text-muted">{{ $lead->secondary_phone }}</span>
                                                    </div>
                                                    @endif
                                                    @if($lead->other_phone)
                                                    <div class="d-flex align-items-center">
                                                        <i data-feather="phone-outgoing" class="text-muted me-2" style="width: 14px; height: 14px;"></i>
                                                        <span class="text-muted">{{ $lead->other_phone }}</span>
                                                    </div>
                                                    @endif
                                                    @if($lead->email)
                                                    <div class="d-flex align-items-center">
                                                        <i data-feather="mail" class="text-muted me-2" style="width: 14px; height: 14px;"></i>
                                                        <span class="text-muted">{{ $lead->email }}</span>
                                                    </div>
                                                    @endif
                                                    @if($lead->address)
                                                    <div class="d-flex align-items-start">
                                                        <i data-feather="map-pin" class="text-muted me-2 mt-1" style="width: 14px; height: 14px;"></i>
                                                        <span class="text-muted">{{ $lead->address }}</span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column - Travel & Assignment Details -->
                                        <div class="col-md-6">
                                            <div class="mb-3 pb-3 border-bottom">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i data-feather="briefcase" class="text-info me-2" style="width: 16px; height: 16px;"></i>
                                                    <small class="text-muted text-uppercase fw-semibold">Travel Details</small>
                                                </div>
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-muted me-2" style="min-width: 100px;">Service:</span>
                                                        <span class="fw-semibold">{{ $lead->service?->name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-muted me-2" style="min-width: 100px;">Destination:</span>
                                                        <span class="fw-semibold">{{ $lead->destination?->name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-muted me-2" style="min-width: 100px;">Travel Date:</span>
                                                        <span class="fw-semibold">{{ $lead->travel_date ? $lead->travel_date->format('d M, Y') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-muted me-2" style="min-width: 100px;">Travelers:</span>
                                                        <div class="d-flex gap-3">
                                                            <span class="badge bg-light text-dark">
                                                                <i data-feather="users" class="me-1" style="width: 12px; height: 12px;"></i>
                                                                Adults: {{ $lead->adults ?? 0 }}
                                                            </span>
                                                            <span class="badge bg-light text-dark">
                                                                <i data-feather="smile" class="me-1" style="width: 12px; height: 12px;"></i>
                                                                Children: {{ $lead->children ?? 0 }}
                                                            </span>
                                                            <span class="badge bg-light text-dark">
                                                                <i data-feather="heart" class="me-1" style="width: 12px; height: 12px;"></i>
                                                                Infants: {{ $lead->infants ?? 0 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 pb-3 border-bottom">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i data-feather="user-check" class="text-success me-2" style="width: 16px; height: 16px;"></i>
                                                    <small class="text-muted text-uppercase fw-semibold">Assignment</small>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        @php
                                                            $assignedEmployee = $lead->assigned_employee;
                                                            $currentEmployee = Auth::user();
                                                            $currentUserId = $currentEmployee ? (\App\Models\User::where('user_id', $currentEmployee->user_id)->orWhere('email', $currentEmployee->login_work_email)->first()?->id ?? null) : null;
                                                        @endphp
                                                        @if($assignedEmployee && $lead->assigned_user_id == $currentUserId)
                                                            <span class="badge bg-success text-white fw-bold px-3 py-2">
                                                                <i data-feather="user-check" style="width: 14px; height: 14px; vertical-align: middle;"></i>
                                                                {{ $assignedEmployee->name }}
                                                            </span>
                                                        @elseif($assignedEmployee)
                                                            <span class="badge bg-secondary text-white px-3 py-2">
                                                                {{ $assignedEmployee->name }}
                                                            </span>
                                                        @elseif($lead->assignedUser)
                                                            <span class="badge bg-secondary text-white px-3 py-2">
                                                                {{ $lead->assignedUser->name }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-light text-dark px-3 py-2">
                                                                Unassigned
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @can('edit leads')
                                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changeAssignedUserModal">
                                                            <i data-feather="edit-2" style="width: 14px; height: 14px;"></i> Change
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>

                                            <div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <i data-feather="tag" class="text-warning me-2" style="width: 16px; height: 16px;"></i>
                                                    <small class="text-muted text-uppercase fw-semibold">Status</small>
                                                </div>
                                                @php
                                                    $statusColors = [
                                                        'new' => 'bg-info text-white',
                                                        'contacted' => 'bg-primary text-white',
                                                        'follow_up' => 'bg-warning text-dark',
                                                        'priority' => 'bg-danger text-white',
                                                        'booked' => 'bg-success text-white',
                                                        'closed' => 'bg-secondary text-white'
                                                    ];
                                                    $color = $statusColors[$lead->status] ?? 'bg-primary text-white';
                                                @endphp
                                                <span class="badge {{ $color }} px-3 py-2 small">
                                                    {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="mt-4 pt-3 border-top">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <form action="{{ route('leads.updateStatus', $lead->id) }}" method="POST">
                                                    @csrf
                                                    <div class="row align-items-end g-3">
                                                        <div class="col-md-5">
                                                            <label class="form-label small text-muted text-uppercase fw-semibold mb-1">Change Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>New</option>
                                                                <option value="contacted" {{ $lead->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                                                <option value="follow_up" {{ $lead->status == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                                                                <option value="priority" {{ $lead->status == 'priority' ? 'selected' : '' }}>Priority</option>
                                                                <option value="booked" {{ $lead->status == 'booked' ? 'selected' : '' }}>Booked</option>
                                                                <option value="closed" {{ $lead->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                                                <option value="cancelled" {{ $lead->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                                <option value="refunded" {{ $lead->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="form-label small text-muted text-uppercase fw-semibold mb-1">Note (Optional)</label>
                                                            <input type="text" name="note" class="form-control" placeholder="Status change note">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="submit" class="btn btn-primary w-100">
                                                                <i data-feather="check" style="width: 14px; height: 14px;"></i> Update
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs for different modules -->
                            <ul class="nav nav-tabs mb-3" id="leadTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="remarks-tab" data-bs-toggle="tab" data-bs-target="#remarks" type="button">Remarks</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button">Payments</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="costs-tab" data-bs-toggle="tab" data-bs-target="#costs" type="button">Cost Components</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="operations-tab" data-bs-toggle="tab" data-bs-target="#operations" type="button">Operations</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">Documents</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery" type="button">Delivery</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">History</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="leadTabsContent">
                                <!-- Remarks Tab -->
                                <div class="tab-pane fade show active" id="remarks" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5 class="mb-0">Remarks & Follow-ups</h5>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $nextFollow = $lead->remarks->filter(fn($r) => $r->follow_up_at && $r->follow_up_at->isFuture())->sortBy('follow_up_at')->first();
                                                @endphp
                                                @if($nextFollow)
                                                    <span class="badge bg-danger me-2">Follow-up: {{ $nextFollow->follow_up_at->format('d M, Y h:i A') }}</span>
                                                @endif
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRemarkModal">Add Remark</button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @forelse($lead->remarks as $remark)
                                                <div class="card mb-2">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <strong>{{ $remark->user->name }}</strong>
                                                                @if($remark->follow_up_at)
                                                                    <span class="badge bg-danger ms-2">Follow-up: {{ $remark->follow_up_at->format('d M, Y h:i A') }}</span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted">{{ $remark->created_at->format('d M, Y h:i A') }}</small>
                                                        </div>
                                                        <p class="mt-2 mb-0">{{ $remark->remark }}</p>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted">No remarks yet.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Payments Tab -->
                                <div class="tab-pane fade" id="payments" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5 class="mb-0">Payments</h5>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">Add Payment</button>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Amount</th>
                                                        <th>Method</th>
                                                        <th>Paid On</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($lead->payments as $payment)
                                                        <tr>
                                                            <td>₹{{ number_format($payment->amount, 2) }}</td>
                                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</td>
                                                            <td>{{ $payment->paid_on->format('d M, Y') }}</td>
                                                            <td>{{ $payment->due_date ? $payment->due_date->format('d M, Y') : 'N/A' }}</td>
                                                            <td><span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'overdue' ? 'danger' : 'warning') }}">{{ ucfirst($payment->status) }}</span></td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-primary edit-payment" data-payment='@json($payment)'>Edit</button>
                                                                <form action="{{ route('leads.payments.destroy', [$lead->id, $payment->id]) }}" method="POST" class="d-inline">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this payment?')">Delete</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr><td colspan="6" class="text-center">No payments recorded</td></tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cost Components Tab -->
                                <div class="tab-pane fade" id="costs" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5 class="mb-0">Cost Components</h5>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCostModal">Add Cost</button>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Description</th>
                                                        <th>Amount</th>
                                                        <th>Entered By</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($lead->costComponents as $cost)
                                                        <tr>
                                                            <td>{{ ucfirst($cost->type) }}</td>
                                                            <td>{{ $cost->description }}</td>
                                                            <td>₹{{ number_format($cost->amount, 2) }}</td>
                                                            <td>{{ $cost->enteredBy->name }}</td>
                                                            <td>{{ $cost->created_at->format('d M, Y') }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-primary edit-cost" data-cost='@json($cost)'>Edit</button>
                                                                <form action="{{ route('leads.cost-components.destroy', [$lead->id, $cost->id]) }}" method="POST" class="d-inline">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this cost?')">Delete</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr><td colspan="6" class="text-center">No cost components recorded</td></tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Operations Tab -->
                                <div class="tab-pane fade" id="operations" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5 class="mb-0">Operations</h5>
                                            @if(!$lead->operation)
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addOperationModal">Create Operation</button>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            @if($lead->operation)
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Status</th>
                                                            <th>Nett Cost</th>
                                                            <th>Profit/Loss</th>
                                                            <th>Approval Status</th>
                                                            <th>Internal Notes</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $lead->operation->operation_status)) }}</span>
                                                            </td>
                                                            <td>₹{{ number_format($lead->operation->nett_cost ?? 0, 2) }}</td>
                                                            <td>
                                                                @php
                                                                    $profit = ($lead->selling_price ?? 0) - ($lead->operation->nett_cost ?? 0);
                                                                    $profitClass = $profit >= 0 ? 'text-success' : 'text-danger';
                                                                @endphp
                                                                <span class="{{ $profitClass }} fw-bold">
                                                                    {{ $profit >= 0 ? '+' : '' }}₹{{ number_format($profit, 2) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($lead->operation->admin_approval_required)
                                                                    <span class="badge bg-warning text-dark">Approval Required</span>
                                                                    @if($lead->operation->approval_reason)
                                                                        <br><small class="text-muted">{{ $lead->operation->approval_reason }}</small>
                                                                    @endif
                                                                @elseif($lead->operation->approval_approved_by)
                                                                    <span class="badge bg-success">Approved</span>
                                                                @else
                                                                    <span class="badge bg-secondary">No Approval Needed</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $lead->operation->internal_notes ?? '-' }}</td>
                                                            <td>
                                                                @if($lead->operation->admin_approval_required)
                                                                    @can('approve operations')
                                                                        <form action="{{ route('leads.operations.approve', [$lead->id, $lead->operation->id]) }}" method="POST" class="d-inline mb-1">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                                        </form>
                                                                        <form action="{{ route('leads.operations.reject', [$lead->id, $lead->operation->id]) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            <input type="text" name="rejection_reason" class="form-control form-control-sm d-inline-block mb-1" placeholder="Rejection reason" style="width: 150px;" required>
                                                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                                                        </form>
                                                                    @endcan
                                                                @endif
                                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editOperationModal">Edit</button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            @else
                                                <p class="text-muted">No operation record. Create one to track nett cost.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents Tab -->
                                <div class="tab-pane fade" id="documents" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Document Checklist</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Document Type</th>
                                                        <th>Status</th>
                                                        <th>Received By</th>
                                                        <th>Verified By</th>
                                                        <th>Received At</th>
                                                        <th>Notes</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $defaultDocuments = ['Passport', 'Visa', 'Ticket', 'Voucher', 'Invoice', 'Identity Proof', 'Address Proof', 'Insurance', 'Medical Certificate'];
                                                        $existingDocuments = $lead->documents->keyBy('type');
                                                    @endphp
                                                    @foreach($defaultDocuments as $docType)
                                                        @php
                                                            $document = $existingDocuments->get($docType);
                                                        @endphp
                                                        <tr>
                                                            <td><strong>{{ $docType }}</strong></td>
                                                            <td>
                                                                @if($document)
                                                                    @php
                                                                        $statusColors = [
                                                                            'not_received' => 'bg-secondary text-white',
                                                                            'received' => 'bg-info text-white',
                                                                            'verified' => 'bg-success text-white',
                                                                            'rejected' => 'bg-danger text-white'
                                                                        ];
                                                                        $color = $statusColors[$document->status] ?? 'bg-secondary text-white';
                                                                    @endphp
                                                                    <span class="badge {{ $color }}">
                                                                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-secondary text-white">Not Received</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $document->receivedBy?->name ?? '-' }}</td>
                                                            <td>{{ $document->verifiedBy?->name ?? '-' }}</td>
                                                            <td>
                                                                @if($document && $document->received_at)
                                                                    {{ $document->received_at->format('d M, Y h:i A') }}
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($document && $document->notes)
                                                                    <span title="{{ $document->notes }}">{{ Str::limit($document->notes, 30) }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @can('verify documents')
                                                                    @if($document)
                                                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDocumentModal{{ $document->id }}">Update Status</button>
                                                                    @else
                                                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createDocumentModal{{ str_replace(' ', '', $docType) }}">Mark Received</button>
                                                                    @endif
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery Tab -->
                                <div class="tab-pane fade" id="delivery" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5 class="mb-0">Delivery</h5>
                                            @if(!$lead->delivery)
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDeliveryModal">Assign Delivery</button>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            @if($lead->delivery)
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Status</th>
                                                            <th>Assigned To</th>
                                                            <th>Expected Delivery</th>
                                                            <th>Actual Delivery</th>
                                                            <th>Courier ID</th>
                                                            <th>Tracking Info</th>
                                                            <th>Notes</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $lead->delivery->delivery_status ?? 'Pending')) }}</span>
                                                            </td>
                                                            <td>{{ $lead->delivery->assignedTo?->name ?? 'N/A' }}</td>
                                                            <td>{{ $lead->delivery->expected_delivery_date ? $lead->delivery->expected_delivery_date->format('d M, Y') : 'N/A' }}</td>
                                                            <td>{{ $lead->delivery->actual_delivery_date ? $lead->delivery->actual_delivery_date->format('d M, Y') : 'N/A' }}</td>
                                                            <td>{{ $lead->delivery->courier_id ?? 'N/A' }}</td>
                                                            <td>{{ $lead->delivery->tracking_info ?? 'N/A' }}</td>
                                                            <td>{{ $lead->delivery->delivery_notes ?? '-' }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDeliveryModal">Edit</button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            @else
                                                <p class="text-muted">No delivery assigned yet.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- History Tab -->
                                <div class="tab-pane fade" id="history" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Status History</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="timeline">
                                                @forelse($lead->histories as $history)
                                                    <div class="timeline-item mb-4 pb-3 border-bottom">
                                                        <div class="d-flex align-items-start">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="avatar avatar-rounded rounded-3 avatar-sm" style="background-color: #007d88;">
                                                                    <span class="initial-wrap text-white fw-bold">{{ strtoupper(substr($history->changedBy->name ?? 'U', 0, 1)) }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                    <div>
                                                                        <h6 class="mb-1 fw-bold">{{ $history->changedBy->name ?? 'Unknown User' }}</h6>
                                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                            @if($history->from_status)
                                                                                <span class="badge bg-secondary text-white">{{ ucfirst(str_replace('_', ' ', $history->from_status)) }}</span>
                                                                                <span class="text-muted">→</span>
                                                                            @endif
                                                                            <span class="badge bg-primary text-white">{{ ucfirst(str_replace('_', ' ', $history->to_status)) }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <small class="text-muted">{{ $history->created_at->format('d M, Y h:i A') }}</small>
                                                                </div>
                                                                @if($history->note)
                                                                    <div class="mt-2 p-2 bg-light rounded">
                                                                        <p class="mb-0 text-muted">{{ $history->note }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center py-5">
                                                        <i data-feather="clock" style="width: 48px; height: 48px; opacity: 0.3;" class="mb-2"></i>
                                                        <p class="text-muted mb-0">no records found</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
</div>

<!-- Add Remark Modal -->
<div class="modal fade" id="addRemarkModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.remarks.store', $lead->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Remark</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Remark</label>
                        <textarea name="remark" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Follow-up Date (Optional)</label>
                        <input type="date" name="follow_up_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Remark</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.payments.store', $lead->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paid On</label>
                        <input type="date" name="paid_on" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date (Optional)</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Cost Component Modal -->
<div class="modal fade" id="addCostModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.cost-components.store', $lead->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Cost Component</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="hotel">Hotel</option>
                            <option value="transport">Transport</option>
                            <option value="visa">Visa</option>
                            <option value="insurance">Insurance</option>
                            <option value="meal">Meal</option>
                            <option value="guide">Guide</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Cost</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Operation Modal -->
<div class="modal fade" id="addOperationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.operations.store', $lead->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Operation Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Operation Status</label>
                        <select name="operation_status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nett Cost</label>
                        <input type="number" name="nett_cost" class="form-control" step="0.01">
                        <small class="text-muted">If nett cost exceeds selling price, approval will be required.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Internal Notes</label>
                        <textarea name="internal_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Document Modal for Default Documents -->
@foreach(['Passport', 'Visa', 'Ticket', 'Voucher', 'Invoice', 'Identity Proof', 'Address Proof', 'Insurance', 'Medical Certificate'] as $docType)
@if(!$lead->documents->where('type', $docType)->first())
<div class="modal fade" id="createDocumentModal{{ str_replace(' ', '', $docType) }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.documents.store', $lead->id) }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $docType }}">
                <div class="modal-header">
                    <h5 class="modal-title">Mark {{ $docType }} as Received</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="received" selected>Received</option>
                            <option value="not_received">Not Received</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this document..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Edit Document Status Modals -->
@foreach($lead->documents as $document)
<div class="modal fade" id="editDocumentModal{{ $document->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.documents.update', [$lead->id, $document->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Document Status - {{ $document->type }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Document Type</label>
                        <input type="text" name="type" class="form-control" value="{{ $document->type }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="not_received" {{ $document->status == 'not_received' ? 'selected' : '' }}>Not Received</option>
                            <option value="received" {{ $document->status == 'received' ? 'selected' : '' }}>Received</option>
                            <option value="verified" {{ $document->status == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="rejected" {{ $document->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $document->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Add Delivery Modal -->
<div class="modal fade" id="addDeliveryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.deliveries.store', $lead->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_to" class="form-select" required>
                            @foreach(\App\Models\User::where('role', 'like', '%Delivery%')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Notes</label>
                        <textarea name="delivery_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Operation Modal -->
@if($lead->operation)
<div class="modal fade" id="editOperationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.operations.update', [$lead->id, $lead->operation->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Operation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Operation Status</label>
                        <select name="operation_status" class="form-select" required>
                            <option value="pending" {{ $lead->operation->operation_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $lead->operation->operation_status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $lead->operation->operation_status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $lead->operation->operation_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nett Cost</label>
                        <input type="number" name="nett_cost" class="form-control" step="0.01" value="{{ $lead->operation->nett_cost }}">
                        <small class="text-muted">If nett cost exceeds selling price, approval will be required.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Internal Notes</label>
                        <textarea name="internal_notes" class="form-control" rows="3">{{ $lead->operation->internal_notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Edit Delivery Modal -->
@if($lead->delivery)
<div class="modal fade" id="editDeliveryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.deliveries.update', [$lead->id, $lead->delivery->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_to" class="form-select" required>
                            @foreach(\App\Models\User::where('role', 'like', '%Delivery%')->orWhere('role', 'like', '%Post Sales%')->get() as $user)
                                <option value="{{ $user->id }}" {{ $lead->delivery->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="delivery_status" class="form-select" required>
                            <option value="Pending" {{ ($lead->delivery->delivery_status ?? 'Pending') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In_Process" {{ ($lead->delivery->delivery_status ?? 'Pending') == 'In_Process' ? 'selected' : '' }}>In Process</option>
                            <option value="Delivered" {{ ($lead->delivery->delivery_status ?? 'Pending') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery_date" class="form-control" value="{{ $lead->delivery->expected_delivery_date ? $lead->delivery->expected_delivery_date->format('Y-m-d') : '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Actual Delivery Date</label>
                        <input type="date" name="actual_delivery_date" class="form-control" value="{{ $lead->delivery->actual_delivery_date ? $lead->delivery->actual_delivery_date->format('Y-m-d') : '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Courier ID</label>
                        <input type="text" name="courier_id" class="form-control" value="{{ $lead->delivery->courier_id }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tracking Info</label>
                        <input type="text" name="tracking_info" class="form-control" value="{{ $lead->delivery->tracking_info }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Notes</label>
                        <textarea name="delivery_notes" class="form-control" rows="2">{{ $lead->delivery->delivery_notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Change Assigned User Modal -->
@can('edit leads')
<div class="modal fade" id="changeAssignedUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leads.updateAssignedUser', $lead->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Assigned User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_user_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach(\App\Models\User::whereNotNull('user_id')->orderBy('name')->get() as $employee)
                                @php
                                    $matchingUser = \App\Models\User::where('email', $employee->login_work_email)
                                        ->orWhere('user_id', $employee->user_id)
                                        ->first();
                                    $isSelected = false;
                                    if ($lead->assigned_user_id && $matchingUser && $lead->assigned_user_id == $matchingUser->id) {
                                        $isSelected = true;
                                    }
                                @endphp
                                <option value="{{ $employee->id }}" 
                                    data-user-id="{{ $matchingUser->id ?? '' }}"
                                    {{ $isSelected ? 'selected' : '' }}>
                                    {{ $employee->name }} @if($employee->user_id)({{ $employee->user_id }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <small>Current assigned user: <strong>{{ $lead->assigned_employee?->name ?? $lead->assignedUser?->name ?? 'Unassigned' }}</strong></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<script>
    $(document).ready(function() {
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection

