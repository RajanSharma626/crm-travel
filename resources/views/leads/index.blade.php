@extends('layouts.app')
@section('title', 'Leads | Travel Shravel')
@section('content')
    @php
        $canEditLeads =
            Auth::user()->can('edit leads') ||
            Auth::user()->hasRole('Customer Care') ||
            Auth::user()->department === 'Customer Care' ||
            Auth::user()->hasRole('Sales') ||
            Auth::user()->department === 'Sales';
        $canDeleteLeads =
            Auth::user()->hasRole('Admin') ||
            Auth::user()->hasRole('Developer') ||
            Auth::user()->department === 'Admin';
    @endphp
    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="remarkToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 300px;"
            data-bs-autohide="true" data-bs-delay="3000">
            <div class="toast-header">
                <i data-feather="info" class="me-2" style="width: 16px; height: 16px;"></i>
                <strong class="me-auto" id="remarkToastTitle">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="remarkToastBody">
                <!-- Toast message will be inserted here -->
            </div>
        </div>
    </div>

    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>There were some problems with your submission:</strong>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="GET" action="{{ route($indexRoute ?? 'leads.index') }}" class="row g-3 mb-4"
                                    id="leadFiltersForm">
                                    <div class="col-md-3 col-lg-2">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select form-select-sm">
                                            <option value="">-- All --</option>
                                            @foreach ($statuses as $key => $label)
                                                <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>
                                                    {{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="service_id" class="form-label">Service</label>
                                        <select name="service_id" id="service_id" class="form-select form-select-sm">
                                            <option value="">-- All --</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}" @selected(($filters['service_id'] ?? '') == $service->id)>
                                                    {{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="destination_id" class="form-label">Destination</label>
                                        <select name="destination_id" id="destination_id"
                                            class="form-select form-select-sm">
                                            <option value="">-- All --</option>
                                            @foreach ($destinations as $destination)
                                                <option value="{{ $destination->id }}" @selected(($filters['destination_id'] ?? '') == $destination->id)>
                                                    {{ $destination->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <label for="search" class="form-label">Search</label>
                                        <div class="d-flex">
                                            <input type="text" name="search" id="search"
                                                class="form-control form-control-sm"
                                                placeholder="Enter name, ref no., or phone"
                                                value="{{ $filters['search'] ?? '' }}">
                                            <button type="submit" class="btn btn-primary btn-sm ms-2 d-flex"> <i
                                                    class="ri-search-line me-1"></i> Filter</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2 align-self-end">
                                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                                            data-bs-target="#addLeadModal">+ Add Lead</button>
                                    </div>
                                    @if ($filters['status'] || $filters['search'] || $filters['service_id'] || $filters['destination_id'])
                                        <div class="col-md-3 col-lg-2 align-self-end ms-auto">
                                            <a href="{{ route($indexRoute ?? 'leads.index') }}"
                                                class="btn btn-outline-danger w-100 btn-sm">Clear
                                                Filters</a>
                                        </div>
                                    @endif
                                </form>

                                <!-- Bulk Actions Bar -->
                                @if ($canEditLeads)
                                    <div id="bulkActionsBar" class="alert alert-info mb-3 d-none" role="alert">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <strong><span id="selectedCount">0</span> lead(s) selected</strong>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-primary" id="bulkAssignBtn"
                                                    data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                                                    <i data-feather="user-plus" style="width: 14px; height: 14px;"></i>
                                                    Assign
                                                    User
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    id="clearSelectionBtn">
                                                    Clear Selection
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (isset($leads) && $leads->count() > 0)
                                    <div class="text-muted small mb-2 px-3">
                                        Showing {{ $leads->firstItem() ?? 0 }} out of {{ $leads->total() }}
                                    </div>
                                @endif

                                <table class="table table-striped small table-bordered w-100 mb-5" id="leadsTable">
                                    <thead>
                                        <tr>
                                            @if ($canEditLeads)
                                                <th width="40">
                                                    <input type="checkbox" id="selectAllLeads" class="form-check-input">
                                                </th>
                                            @endif
                                            <th>Ref No.</th>
                                            <th>Customer Name</th>
                                            <th>Phone</th>
                                            <th>Service</th>
                                            <th>Destination</th>
                                            <th>Status</th>
                                            <th>Remark</th>
                                            <th>Created On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leads as $lead)
                                            <tr>
                                                @if ($canEditLeads)
                                                    <td>
                                                        <input type="checkbox" class="form-check-input lead-checkbox"
                                                            value="{{ $lead->id }}"
                                                            data-lead-name="{{ $lead->customer_name }}">
                                                    </td>
                                                @endif
                                                <td><strong>{{ $lead->tsq }}</strong></td>
                                                <td>
                                                    <a href="#"
                                                        class="text-primary text-decoration-none fw-semibold view-lead-btn lead-name-link"
                                                        data-lead-id="{{ $lead->id }}">
                                                        {{ $lead->salutation ? $lead->salutation . ' ' : '' }}{{ $lead->customer_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $lead->primary_phone ?? $lead->phone }}
                                                    @if ($lead->primary_phone || $lead->phone || $lead->secondary_phone || $lead->other_phone)
                                                        <a href="#"
                                                            class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover ms-1 send-sms-btn"
                                                            data-lead-id="{{ $lead->id }}"
                                                            data-phone="{{ $lead->primary_phone ?? $lead->phone }}"
                                                            data-secondary-phone="{{ $lead->secondary_phone ?? '' }}"
                                                            data-other-phone="{{ $lead->other_phone ?? '' }}"
                                                            data-bs-toggle="tooltip" data-placement="top"
                                                            title="Send SMS">
                                                            <span class="icon">
                                                                <span class="feather-icon">
                                                                    <i data-feather="message-square"></i>
                                                                </span>
                                                            </span>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $lead->service ? $lead->service->name : '-' }}</td>
                                                <td>{{ $lead->destination ? $lead->destination->name : '-' }}</td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'new' => 'bg-info text-white',
                                                            'contacted' => 'bg-primary text-white',
                                                            'follow_up' => 'bg-warning text-dark',
                                                            'priority' => 'bg-danger text-white',
                                                            'booked' => 'bg-success text-white',
                                                            'closed' => 'bg-secondary text-white',
                                                        ];
                                                        $color =
                                                            $statusColors[$lead->status] ?? 'bg-primary text-white';
                                                    @endphp
                                                    <span class="badge {{ $color }}">
                                                        {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($lead->latest_remark)
                                                        <div class="text-truncate" style="max-width: 200px;"
                                                            title="{{ $lead->latest_remark->remark }}">
                                                            {{ Str::limit($lead->latest_remark->remark, 40) }}
                                                        </div>
                                                        <small class="text-muted">
                                                            by
                                                            {{ $lead->latest_remark->employee?->name ?? ($lead->latest_remark->user?->name ?? 'N/A') }}
                                                            @if ($lead->latest_remark->created_at)
                                                                - {{ $lead->latest_remark->created_at->format('d M, y') }}
                                                            @endif
                                                        </small>
                                                    @else
                                                        <span class="text-muted">No remarks yet</span>
                                                    @endif
                                                </td>
                                                <td>{{ $lead->created_at->format('d M, y') }}</td>
                                                <td>
                                                    @php
                                                        $employee = Auth::user();
                                                        $role = $employee->role ?? $employee->getRoleNameAttribute();
                                                        $nonSalesDepartments = [
                                                            'Operation',
                                                            'Operation Manager',
                                                            'Delivery',
                                                            'Delivery Manager',
                                                            'Post Sales',
                                                            'Post Sales Manager',
                                                            'Accounts',
                                                            'Accounts Manager',
                                                        ];
                                                        $isNonSalesDept =
                                                            $role && in_array($role, $nonSalesDepartments);
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex">
                                                            @if (!$isNonSalesDept)
                                                                <a href="#"
                                                                    class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover view-lead-btn"
                                                                    data-lead-id="{{ $lead->id }}"
                                                                    data-bs-toggle="tooltip" data-placement="top"
                                                                    title="View Lead">
                                                                    <span class="icon">
                                                                        <span class="feather-icon">
                                                                            <i data-feather="eye"></i>
                                                                        </span>
                                                                    </span>
                                                                </a>

                                                                @if ($canEditLeads)
                                                                    <a href="#"
                                                                        class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover edit-lead-btn"
                                                                        data-lead-id="{{ $lead->id }}"
                                                                        data-bs-toggle="tooltip" data-placement="top"
                                                                        title="Edit Lead">
                                                                        <span class="icon">
                                                                            <span class="feather-icon">
                                                                                <i data-feather="edit"></i>
                                                                            </span>
                                                                        </span>
                                                                    </a>
                                                                    @if (!$lead->assigned_user_id)
                                                                        <a href="#"
                                                                            class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover assign-user-btn"
                                                                            data-lead-id="{{ $lead->id }}"
                                                                            data-lead-name="{{ $lead->customer_name }}"
                                                                            data-current-user="{{ $lead->assigned_employee?->name ?? ($lead->assignedUser?->name ?? 'Unassigned') }}"
                                                                            data-bs-toggle="tooltip" data-placement="top"
                                                                            title="Assign Agent">
                                                                            <span class="icon">
                                                                                <span class="feather-icon">
                                                                                    <i data-feather="user-plus"></i>
                                                                                </span>
                                                                            </span>
                                                                        </a>
                                                                    @endif
                                                                @endif
                                                                @if ($canDeleteLeads)
                                                                    <form
                                                                        action="{{ route($destroyRoute ?? 'leads.destroy', $lead) }}"
                                                                        method="POST" class="d-inline delete-lead-form"
                                                                        onsubmit="return confirm('Are you sure you want to delete lead {{ $lead->tsq }}? This action cannot be undone.');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-icon btn-flush-danger btn-rounded flush-soft-hover text-danger"
                                                                            data-bs-toggle="tooltip" data-placement="top"
                                                                            title="Delete Lead"
                                                                            style="color: #dc3545 !important;">
                                                                            <span class="icon">
                                                                                <span class="feather-icon">
                                                                                    <i data-feather="trash-2"></i>
                                                                                </span>
                                                                            </span>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @endif

                                                            @if ($lead->status == 'booked' && !Auth::user()->hasRole('Customer Care') && !request()->routeIs('customer-care.*'))
                                                                <a href="{{ route('bookings.form', $lead) }}"
                                                                    class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover text-primary"
                                                                    data-bs-toggle="tooltip" data-placement="top"
                                                                    title="Booking File">
                                                                    <span class="icon">
                                                                        <span class="feather-icon">
                                                                            <i data-feather="file-text"></i>
                                                                        </span>
                                                                    </span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ $canEditLeads ? 10 : 9 }}" class="text-center">no records
                                                    found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mb-3 px-3">
                                    {{ $leads->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    <!-- Add Lead Modal -->
    <div class="modal fade" id="addLeadModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLeadModalLabel">Add Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route($storeRoute ?? 'leads.store') }}" method="POST" id="addLeadForm">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" name="lead_id" id="editLeadId" value="">
                        <input type="hidden" name="children" id="children_total" value="{{ old('children', 0) }}">

                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Customer Information</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Ref. No.</label>
                                    <input type="text" id="addRefNo" class="form-control form-control-sm" readonly
                                        style="background-color: #f8f9fa; cursor: not-allowed;" placeholder="TSQ Number">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Salutation</label>
                                    <select name="salutation" class="form-select form-select-sm">
                                        <option value="">-- Select --</option>
                                        <option value="Mr" {{ old('salutation') == 'Mr' ? 'selected' : '' }}>Mr
                                        </option>
                                        <option value="Mrs" {{ old('salutation') == 'Mrs' ? 'selected' : '' }}>Mrs
                                        </option>
                                        <option value="Ms" {{ old('salutation') == 'Ms' ? 'selected' : '' }}>Ms
                                        </option>
                                        <option value="Dr" {{ old('salutation') == 'Dr' ? 'selected' : '' }}>Dr
                                        </option>
                                        <option value="Prof" {{ old('salutation') == 'Prof' ? 'selected' : '' }}>Prof
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" placeholder="e.g. Ramesh"
                                        class="form-control form-control-sm" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" placeholder="e.g. Kumar"
                                        class="form-control form-control-sm" value="{{ old('last_name') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 border rounded-3 p-3">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Contact Information</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Primary Number <span class="text-danger">*</span></label>
                                    <input type="text" name="primary_phone" placeholder="+91 98765 43210"
                                        class="form-control form-control-sm" value="{{ old('primary_phone') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Secondary Number</label>
                                    <input type="text" name="secondary_phone" placeholder="Alternate contact"
                                        class="form-control form-control-sm" value="{{ old('secondary_phone') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Emergency No.</label>
                                    <input type="text" name="other_phone" placeholder="Emergency contact"
                                        class="form-control form-control-sm" value="{{ old('other_phone') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" placeholder="customer@email.com"
                                        class="form-control form-control-sm" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address_line"
                                        placeholder="Street Address, Building, Apartment"
                                        class="form-control form-control-sm" value="{{ old('address_line') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" placeholder="City"
                                        class="form-control form-control-sm" value="{{ old('city') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" placeholder="State"
                                        class="form-control form-control-sm" value="{{ old('state') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Country</label>
                                    <select name="country" class="form-select form-select-sm">
                                        <option value="">-- Select Country --</option>
                                        <option value="Abu Dhabi" {{ old('country') == 'Abu Dhabi' ? 'selected' : '' }}>
                                            Abu Dhabi</option>
                                        <option value="America" {{ old('country') == 'America' ? 'selected' : '' }}>
                                            America</option>
                                        <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>
                                            Australia</option>
                                        <option value="Austria" {{ old('country') == 'Austria' ? 'selected' : '' }}>
                                            Austria</option>
                                        <option value="Azerbaijan" {{ old('country') == 'Azerbaijan' ? 'selected' : '' }}>
                                            Azerbaijan</option>
                                        <option value="Belgium" {{ old('country') == 'Belgium' ? 'selected' : '' }}>
                                            Belgium</option>
                                        <option value="Bhutan" {{ old('country') == 'Bhutan' ? 'selected' : '' }}>Bhutan
                                        </option>
                                        <option value="Cambodia" {{ old('country') == 'Cambodia' ? 'selected' : '' }}>
                                            Cambodia</option>
                                        <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada
                                        </option>
                                        <option value="Croatia" {{ old('country') == 'Croatia' ? 'selected' : '' }}>
                                            Croatia</option>
                                        <option value="Denmark" {{ old('country') == 'Denmark' ? 'selected' : '' }}>
                                            Denmark</option>
                                        <option value="Dubai" {{ old('country') == 'Dubai' ? 'selected' : '' }}>Dubai
                                        </option>
                                        <option value="Finland" {{ old('country') == 'Finland' ? 'selected' : '' }}>
                                            Finland</option>
                                        <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>France
                                        </option>
                                        <option value="Georgia" {{ old('country') == 'Georgia' ? 'selected' : '' }}>
                                            Georgia</option>
                                        <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>
                                            Germany</option>
                                        <option value="Greece" {{ old('country') == 'Greece' ? 'selected' : '' }}>Greece
                                        </option>
                                        <option value="Hong Kong" {{ old('country') == 'Hong Kong' ? 'selected' : '' }}>
                                            Hong Kong</option>
                                        <option value="Iceland" {{ old('country') == 'Iceland' ? 'selected' : '' }}>
                                            Iceland</option>
                                        <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India
                                        </option>
                                        <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>
                                            Indonesia</option>
                                        <option value="Ireland" {{ old('country') == 'Ireland' ? 'selected' : '' }}>
                                            Ireland</option>
                                        <option value="Italy" {{ old('country') == 'Italy' ? 'selected' : '' }}>Italy
                                        </option>
                                        <option value="Kazakhstan" {{ old('country') == 'Kazakhstan' ? 'selected' : '' }}>
                                            Kazakhstan</option>
                                        <option value="Laos" {{ old('country') == 'Laos' ? 'selected' : '' }}>Laos
                                        </option>
                                        <option value="Lithuania" {{ old('country') == 'Lithuania' ? 'selected' : '' }}>
                                            Lithuania</option>
                                        <option value="Luxembourg" {{ old('country') == 'Luxembourg' ? 'selected' : '' }}>
                                            Luxembourg</option>
                                        <option value="Macau" {{ old('country') == 'Macau' ? 'selected' : '' }}>Macau
                                        </option>
                                        <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>
                                            Malaysia</option>
                                        <option value="Mauritius" {{ old('country') == 'Mauritius' ? 'selected' : '' }}>
                                            Mauritius</option>
                                        <option value="Moldova" {{ old('country') == 'Moldova' ? 'selected' : '' }}>
                                            Moldova</option>
                                        <option value="Nepal" {{ old('country') == 'Nepal' ? 'selected' : '' }}>Nepal
                                        </option>
                                        <option value="Netherlands"
                                            {{ old('country') == 'Netherlands' ? 'selected' : '' }}>Netherlands</option>
                                        <option value="New Zealand"
                                            {{ old('country') == 'New Zealand' ? 'selected' : '' }}>New Zealand</option>
                                        <option value="Norway" {{ old('country') == 'Norway' ? 'selected' : '' }}>Norway
                                        </option>
                                        <option value="Phu Quoc" {{ old('country') == 'Phu Quoc' ? 'selected' : '' }}>Phu
                                            Quoc</option>
                                        <option value="Poland" {{ old('country') == 'Poland' ? 'selected' : '' }}>Poland
                                        </option>
                                        <option value="Portugal" {{ old('country') == 'Portugal' ? 'selected' : '' }}>
                                            Portugal</option>
                                        <option value="Russia" {{ old('country') == 'Russia' ? 'selected' : '' }}>Russia
                                        </option>
                                        <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>
                                            Singapore</option>
                                        <option value="Spain" {{ old('country') == 'Spain' ? 'selected' : '' }}>Spain
                                        </option>
                                        <option value="Srilanka" {{ old('country') == 'Srilanka' ? 'selected' : '' }}>
                                            Srilanka</option>
                                        <option value="Sweden" {{ old('country') == 'Sweden' ? 'selected' : '' }}>Sweden
                                        </option>
                                        <option value="Switzerland"
                                            {{ old('country') == 'Switzerland' ? 'selected' : '' }}>Switzerland</option>
                                        <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>
                                            Thailand</option>
                                        <option value="Turkey" {{ old('country') == 'Turkey' ? 'selected' : '' }}>Turkey
                                        </option>
                                        <option value="United Kingdom"
                                            {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom
                                        </option>
                                        <option value="Vatican City"
                                            {{ old('country') == 'Vatican City' ? 'selected' : '' }}>Vatican City</option>
                                        <option value="Vietnam" {{ old('country') == 'Vietnam' ? 'selected' : '' }}>
                                            Vietnam</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Pin Code</label>
                                    <input type="text" name="pin_code" placeholder="Pin Code"
                                        class="form-control form-control-sm" value="{{ old('pin_code') }}"
                                        maxlength="20">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Travel Preferences</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Service <span class="text-danger">*</span></label>
                                    <select name="service_id" class="form-select form-select-sm" required>
                                        <option value="">-- Select Service --</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}"
                                                {{ (string) old('service_id') === (string) $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Destination <span class="text-danger">*</span></label>
                                    <select name="destination_id" class="form-select form-select-sm" required>
                                        <option value="">-- Select Destination --</option>
                                        @foreach ($destinations as $destination)
                                            <option value="{{ $destination->id }}"
                                                {{ (string) old('destination_id') === (string) $destination->id ? 'selected' : '' }}>
                                                {{ $destination->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Travel Date <span class="text-danger">*</span></label>
                                    <input type="date" name="travel_date" class="form-control form-control-sm"
                                        value="{{ old('travel_date') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Return Date</label>
                                    <input type="date" name="return_date" class="form-control form-control-sm"
                                        value="{{ old('return_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Adults <span class="text-danger">*</span></label>
                                    <input type="number" name="adults" class="form-control form-control-sm"
                                        min="1" value="{{ old('adults', 1) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Children (2-5 yrs)</label>
                                    <input type="number" name="children_2_5" class="form-control form-control-sm"
                                        min="0" value="{{ old('children_2_5', 0) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Children (6-11 yrs)</label>
                                    <input type="number" name="children_6_11" class="form-control form-control-sm"
                                        min="0" value="{{ old('children_6_11', 0) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Infants (below 2 yrs)</label>
                                    <input type="number" name="infants" class="form-control form-control-sm"
                                        min="0" value="{{ old('infants', 0) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 border rounded-3 p-3">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Assignee</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Assign To</label>
                                    <select name="assigned_user_id" class="form-select form-select-sm">
                                        <option value="">-- Select Responsible Person --</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" data-user-id="{{ $employee->id }}"
                                                data-user-email="{{ $employee->email ?? '' }}"
                                                {{ (string) old('assigned_user_id') === (string) $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }} @if ($employee->user_id)
                                                    ({{ $employee->user_id }} - {{ $employee->department }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select form-select-sm" required>
                                        @foreach ($statuses as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('status', 'new') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="addLeadSubmitBtn">Add Lead</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Lead Modal -->
    <div class="modal fade" id="viewLeadModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="viewLeadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom">
                    <div class="flex-grow-1">
                        <h5 class="modal-title fw-bold mb-1" id="viewLeadModalLabel">
                            <i data-feather="user" class="me-2" style="width: 20px; height: 20px;"></i>
                            <span id="viewLeadModalTitle">Lead Details</span>
                        </h5>
                        <small class="text-muted" id="viewLeadMeta"></small>
                    </div>
                </div>
                <div class="modal-body p-4">
                    <div id="viewLeadAlert" class="alert d-none mb-3" role="alert"></div>

                    <div id="viewLeadLoader" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                        <p class="mt-3 text-muted mb-0">Loading lead details...</p>
                    </div>

                    <div id="viewLeadContent" class="d-none">
                        <!-- Customer Information -->
                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Customer Information</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Ref. No.</label>
                                    <input type="text" id="viewRefNo" class="form-control form-control-sm" readonly
                                        disabled style="background-color: #f8f9fa; cursor: not-allowed;">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Salutation</label>
                                    <input type="text" id="viewSalutation" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" id="viewFirstName" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" id="viewLastName" class="form-control form-control-sm"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-4 border rounded-3 p-3">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Contact Information</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Primary Number</label>
                                    <input type="text" id="viewPrimaryPhone" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Secondary Number</label>
                                    <input type="text" id="viewSecondaryPhone" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Emergency No.</label>
                                    <input type="text" id="viewOtherPhone" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" id="viewEmail" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" id="viewAddressLine" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">City</label>
                                    <input type="text" id="viewCity" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">State</label>
                                    <input type="text" id="viewState" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" id="viewCountry" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Pin Code</label>
                                    <input type="text" id="viewPinCode" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Travel Preferences -->
                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Travel Preferences</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Service</label>
                                    <input type="text" id="viewService" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Destination</label>
                                    <input type="text" id="viewDestination" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Travel Date</label>
                                    <input type="text" id="viewTravelDate" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Return Date</label>
                                    <input type="text" id="viewReturnDate" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Adults</label>
                                    <input type="text" id="viewAdults" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Children (2-5 yrs)</label>
                                    <input type="text" id="viewChildren25" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Children (6-11 yrs)</label>
                                    <input type="text" id="viewChildren611" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Infants (below 2 yrs)</label>
                                    <input type="text" id="viewInfants" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment -->
                        <div class="mb-4 border rounded-3 p-3">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Assignee</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Assign To</label>
                                    <input type="text" id="viewAssignedUser" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <input type="text" id="viewStatus" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Remarks -->
                        <div class="card border-0 shadow-sm">
                            <div
                                class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">
                                    <i data-feather="list" class="me-2 text-warning"
                                        style="width: 18px; height: 18px;"></i>
                                    Recent Remarks
                                </h6>
                                <div class="d-flex align-items-center">
                                </div>
                            </div>
                            <div class="card-body p-4" id="viewLeadRemarks" style="max-height: 400px; overflow-y: auto;">
                                <p class="text-muted text-center mb-0 py-3">No remarks yet.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Lead Form -->
                    <div id="editLeadContent" class="d-none">
                        <div id="editLeadAlert" class="alert d-none mb-3" role="alert"></div>
                        <form id="editLeadForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="children" id="editChildrenTotal" value="0">

                            <!-- Customer Information -->
                            <div class="mb-4 border rounded-3 p-3 bg-light">
                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">Customer Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Ref. No.</label>
                                        <input type="text" id="editRefNo" class="form-control form-control-sm"
                                            readonly style="background-color: #f8f9fa; cursor: not-allowed;"
                                            placeholder="TSQ Number">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Salutation</label>
                                        <select name="salutation" id="editSalutation" class="form-select form-select-sm">
                                            <option value="">-- Select --</option>
                                            <option value="Mr">Mr</option>
                                            <option value="Mrs">Mrs</option>
                                            <option value="Ms">Ms</option>
                                            <option value="Dr">Dr</option>
                                            <option value="Prof">Prof</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="editFirstName"
                                            placeholder="e.g. Ramesh" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="last_name" id="editLastName"
                                            placeholder="e.g. Kumar" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="mb-4 border rounded-3 p-3">
                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">Contact Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Primary Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="primary_phone" id="editPrimaryPhone"
                                            placeholder="+91 98765 43210" class="form-control form-control-sm"
                                            maxlength="20" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Secondary Number</label>
                                        <input type="text" name="secondary_phone" id="editSecondaryPhone"
                                            placeholder="Alternate contact" class="form-control form-control-sm"
                                            maxlength="20">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Emergency No.</label>
                                        <input type="text" name="other_phone" id="editOtherPhone"
                                            placeholder="Emergency contact" class="form-control form-control-sm"
                                            maxlength="20">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="editEmail"
                                            placeholder="customer@email.com" class="form-control form-control-sm"
                                            required>
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-12">
                                        <label class="form-label">Address</label>
                                        <input type="text" name="address_line" id="editAddressLine"
                                            placeholder="Street Address, Building, Apartment"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" id="editCity" placeholder="City"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">State</label>
                                        <input type="text" name="state" id="editState" placeholder="State"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Country</label>
                                        <select name="country" id="editCountry" class="form-select form-select-sm">
                                            <option value="">-- Select Country --</option>
                                            <option value="Abu Dhabi">Abu Dhabi</option>
                                            <option value="America">America</option>
                                            <option value="Australia">Australia</option>
                                            <option value="Austria">Austria</option>
                                            <option value="Azerbaijan">Azerbaijan</option>
                                            <option value="Belgium">Belgium</option>
                                            <option value="Bhutan">Bhutan</option>
                                            <option value="Cambodia">Cambodia</option>
                                            <option value="Canada">Canada</option>
                                            <option value="Croatia">Croatia</option>
                                            <option value="Denmark">Denmark</option>
                                            <option value="Dubai">Dubai</option>
                                            <option value="Finland">Finland</option>
                                            <option value="France">France</option>
                                            <option value="Georgia">Georgia</option>
                                            <option value="Germany">Germany</option>
                                            <option value="Greece">Greece</option>
                                            <option value="Hong Kong">Hong Kong</option>
                                            <option value="Iceland">Iceland</option>
                                            <option value="India">India</option>
                                            <option value="Indonesia">Indonesia</option>
                                            <option value="Ireland">Ireland</option>
                                            <option value="Italy">Italy</option>
                                            <option value="Kazakhstan">Kazakhstan</option>
                                            <option value="Laos">Laos</option>
                                            <option value="Lithuania">Lithuania</option>
                                            <option value="Luxembourg">Luxembourg</option>
                                            <option value="Macau">Macau</option>
                                            <option value="Malaysia">Malaysia</option>
                                            <option value="Mauritius">Mauritius</option>
                                            <option value="Moldova">Moldova</option>
                                            <option value="Nepal">Nepal</option>
                                            <option value="Netherlands">Netherlands</option>
                                            <option value="New Zealand">New Zealand</option>
                                            <option value="Norway">Norway</option>
                                            <option value="Phu Quoc">Phu Quoc</option>
                                            <option value="Poland">Poland</option>
                                            <option value="Portugal">Portugal</option>
                                            <option value="Russia">Russia</option>
                                            <option value="Singapore">Singapore</option>
                                            <option value="Spain">Spain</option>
                                            <option value="Srilanka">Srilanka</option>
                                            <option value="Sweden">Sweden</option>
                                            <option value="Switzerland">Switzerland</option>
                                            <option value="Thailand">Thailand</option>
                                            <option value="Turkey">Turkey</option>
                                            <option value="United Kingdom">United Kingdom</option>
                                            <option value="Vatican City">Vatican City</option>
                                            <option value="Vietnam">Vietnam</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Pin Code</label>
                                        <input type="text" name="pin_code" id="editPinCode" placeholder="Pin Code"
                                            class="form-control form-control-sm" maxlength="20">
                                    </div>
                                </div>
                            </div>

                            <!-- Travel Preferences -->
                            <div class="mb-4 border rounded-3 p-3 bg-light">
                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">Travel Preferences</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Service <span class="text-danger">*</span></label>
                                        <select name="service_id" id="editServiceId" class="form-select form-select-sm"
                                            required>
                                            <option value="">-- Select Service --</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Destination <span class="text-danger">*</span></label>
                                        <select name="destination_id" id="editDestinationId"
                                            class="form-select form-select-sm" required>
                                            <option value="">-- Select Destination --</option>
                                            @foreach ($destinations as $destination)
                                                <option value="{{ $destination->id }}">{{ $destination->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Travel Date <span class="text-danger">*</span></label>
                                        <input type="date" name="travel_date" id="editTravelDate"
                                            class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Return Date</label>
                                        <input type="date" name="return_date" id="editReturnDate"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Adults <span class="text-danger">*</span></label>
                                        <input type="number" name="adults" id="editAdults"
                                            class="form-control form-control-sm" min="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Children (2-5 yrs)</label>
                                        <input type="number" name="children_2_5" id="editChildren25"
                                            class="form-control form-control-sm" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Children (6-11 yrs)</label>
                                        <input type="number" name="children_6_11" id="editChildren611"
                                            class="form-control form-control-sm" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Infants (below 2 yrs)</label>
                                        <input type="number" name="infants" id="editInfants"
                                            class="form-control form-control-sm" min="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Assignment -->
                            <div class="mb-4 border rounded-3 p-3">
                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">Assignment</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Assign To</label>
                                        <select name="assigned_user_id" id="editAssignedEmployeeId"
                                            class="form-select form-select-sm">
                                            <option value="">-- Select Employee --</option>
                                            @foreach ($employees as $employee)
                                                @php
                                                    // Try to find matching user for this employee
                                                    $matchingUser = \App\Models\User::where(
                                                        'email',
                                                        $employee->login_work_email,
                                                    )
                                                        ->orWhere('email', $employee->user_id)
                                                        ->first();
                                                @endphp
                                                <option value="{{ $employee->id }}"
                                                    data-user-id="{{ $matchingUser->id ?? '' }}"
                                                    data-user-email="{{ $employee->login_work_email ?? '' }}">
                                                    {{ $employee->name }} @if ($employee->user_id)
                                                        ({{ $employee->user_id }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="editStatus" class="form-select form-select-sm"
                                            required>
                                            @foreach ($statuses as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light border"
                                    id="cancelEditFormBtn">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Lead</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100">
                        <form id="leadRemarkForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-7 mb-3">
                                    <label class="form-label small mb-1">Add Remark <span
                                            class="text-danger">*</span></label>
                                    <textarea name="remark" class="form-control form-control-sm" rows="2" placeholder="Enter your remark here..."
                                        required></textarea>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small mb-1">Follow-up Date &amp; Time</label>
                                    <input type="datetime-local" name="follow_up_at"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-12 d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                        <i data-feather="x" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i data-feather="send" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Add Remark
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign User Modal -->
    <div class="modal fade" id="assignUserModal" tabindex="-1" aria-labelledby="assignUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="assignUserModalLabel">
                        <i data-feather="user-plus" class="me-2" style="width: 20px; height: 20px;"></i>
                        Assign Lead to User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="assignUserAlert" class="alert d-none" role="alert"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lead</label>
                        <input type="text" class="form-control" id="assignUserLeadName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Assignment</label>
                        <input type="text" class="form-control" id="assignUserCurrentUser" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select" id="assignUserSelect" required>
                            <option value="">-- Select Employee --</option>
                            @foreach ($employees as $employee)
                                @php
                                    $matchingUser = \App\Models\User::where('email', $employee->login_work_email)
                                        ->orWhere('email', $employee->user_id)
                                        ->first();
                                @endphp
                                <option value="{{ $employee->id }}" data-user-id="{{ $matchingUser->id ?? '' }}">
                                    {{ $employee->name }} @if ($employee->user_id)
                                        ({{ $employee->user_id }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="assignUserSubmitBtn">
                        <i data-feather="check" class="me-1" style="width: 16px; height: 16px;"></i>
                        Assign
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Assign Modal -->
    @if ($canEditLeads)
        <div class="modal fade" id="bulkAssignModal" tabindex="-1" aria-labelledby="bulkAssignModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold" id="bulkAssignModalLabel">
                            <i data-feather="user-plus" class="me-2" style="width: 20px; height: 20px;"></i>
                            Bulk Assign Leads
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="bulkAssignAlert" class="alert d-none" role="alert"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Selected Leads</label>
                            <div class="form-control" id="bulkAssignSelectedLeads"
                                style="min-height: 60px; max-height: 150px; overflow-y: auto;" readonly>
                                <small class="text-muted">No leads selected</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Assign To <span class="text-danger">*</span></label>
                            <select class="form-select" id="bulkAssignUserSelect" required>
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $employee)
                                    @php
                                        $matchingUser = \App\Models\User::where('email', $employee->login_work_email)
                                            ->orWhere('email', $employee->user_id)
                                            ->first();
                                    @endphp
                                    <option value="{{ $employee->id }}" data-user-id="{{ $matchingUser->id ?? '' }}">
                                        {{ $employee->name }} @if ($employee->user_id)
                                            ({{ $employee->user_id }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="bulkAssignSubmitBtn">
                            <i data-feather="check" class="me-1" style="width: 16px; height: 16px;"></i>
                            Assign Selected Leads
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Send SMS Modal -->
    <div class="modal fade" id="sendSmsModal" tabindex="-1" aria-labelledby="sendSmsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="sendSmsModalLabel">
                        <i data-feather="message-square" class="me-2" style="width: 20px; height: 20px;"></i>
                        Send SMS
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sendSmsForm">
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2">Select Phone Numbers</label>
                            <div class="bg-light p-3 rounded border">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="selectAllNumbers">
                                    <label class="form-check-label fw-semibold" for="selectAllNumbers">
                                        Select All
                                    </label>
                                </div>
                                <hr class="my-2">
                                <div id="phoneNumbersList">
                                    <!-- Phone numbers will be injected here -->
                                </div>
                            </div>
                        </div>


                        <div class="mb-3">
                            <label class="form-label fw-bold mb-2">Select Message Template</label>
                            <div class="list-group" id="messageTemplatesList"
                                style="max-height: 200px; overflow-y: auto;">
                                <!-- Templates will be loaded dynamically -->
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted small mb-0 mt-2">Loading templates...</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendSmsSubmitBtn">
                        <i data-feather="send" class="me-1" style="width: 16px; height: 16px;"></i>
                        Send SMS
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .lead-name-link {
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .lead-name-link:hover {
                text-decoration: underline !important;
                opacity: 0.8;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Safe Feather icon replacement helper
            function safeFeatherReplace(container) {
                if (typeof feather === 'undefined') return;

                // Use setTimeout to ensure DOM is ready
                setTimeout(function() {
                    try {
                        // Validate icons exist before replacing
                        const selector = container ? container.querySelectorAll('[data-feather]') : document
                            .querySelectorAll('[data-feather]');

                        if (selector && selector.length > 0) {
                            // Check if at least one valid icon exists
                            let hasValidIcon = false;
                            for (let i = 0; i < selector.length; i++) {
                                const icon = selector[i];
                                const iconName = icon.getAttribute('data-feather');
                                if (iconName && feather.icons && feather.icons[iconName]) {
                                    hasValidIcon = true;
                                    break;
                                }
                            }

                            if (hasValidIcon) {
                                // Feather.replace() replaces all icons, but we can scope it
                                if (container) {
                                    // Replace icons only in the specified container
                                    const icons = container.querySelectorAll('[data-feather]');
                                    icons.forEach(function(icon) {
                                        try {
                                            const iconName = icon.getAttribute('data-feather');
                                            if (iconName && feather.icons && feather.icons[iconName] &&
                                                typeof feather.icons[iconName].toSvg === 'function') {
                                                const svg = feather.icons[iconName].toSvg();
                                                if (svg) {
                                                    icon.outerHTML = svg;
                                                }
                                            }
                                        } catch (e) {
                                            // Skip invalid icons silently
                                        }
                                    });
                                } else {
                                    feather.replace();
                                }
                            }
                        }
                    } catch (e) {
                        console.warn('Feather icon replacement error:', e);
                    }
                }, 10);
            }

            $(document).ready(function() {
                // Initialize Feather icons on page load
                safeFeatherReplace();

                // Initialize Bootstrap tooltips on page load
                if (typeof bootstrap !== 'undefined') {
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }



                const addLeadForm = document.getElementById('addLeadForm');
                const addLeadModalEl = document.getElementById('addLeadModal');
                const leadsBaseUrl = @json(url(request()->path()));

                const updateChildrenTotal = () => {
                    if (!addLeadForm) return;
                    const child2_5 = parseInt(addLeadForm.elements['children_2_5']?.value || '0', 10);
                    const child6_11 = parseInt(addLeadForm.elements['children_6_11']?.value || '0', 10);
                    if (addLeadForm.elements['children']) {
                        addLeadForm.elements['children'].value = (child2_5 || 0) + (child6_11 || 0);
                    }
                };


                if (addLeadForm) {
                    addLeadForm.querySelectorAll('input, select, textarea').forEach((field) => {
                        field.addEventListener('input', () => {
                            if (field.name === 'children_2_5' || field.name === 'children_6_11') {
                                updateChildrenTotal();
                            }
                        });
                        field.addEventListener('change', () => {
                            if (field.name === 'children_2_5' || field.name === 'children_6_11') {
                                updateChildrenTotal();
                            }
                        });
                    });

                    addLeadForm.addEventListener('submit', (event) => {
                        updateChildrenTotal();

                        // Check if it's edit mode
                        const formMethod = document.getElementById('formMethod');
                        const editLeadId = document.getElementById('editLeadId');
                        const isEditMode = formMethod && formMethod.value === 'PUT' && editLeadId && editLeadId
                            .value;

                        if (isEditMode) {
                            // Update the form action for edit mode
                            addLeadForm.action = `${leadsBaseUrl}/${editLeadId.value}`;
                            // The _method field is already set to PUT by populateAddLeadFormForEdit
                            // Form will submit directly with PUT method spoofing
                        } else {
                            // Add mode - ensure form action is correct
                            addLeadForm.action = '{{ route($storeRoute ?? 'leads.store') }}';
                            if (formMethod) formMethod.value = 'POST';
                        }
                        // Let the form submit normally (no preventDefault)
                    });
                }

                if (addLeadModalEl && typeof bootstrap !== 'undefined') {
                    addLeadModalEl.addEventListener('shown.bs.modal', () => {
                        updateChildrenTotal();
                    });

                    addLeadModalEl.addEventListener('hidden.bs.modal', () => {
                        // Reset form to add mode when modal is closed
                        resetAddLeadFormToAddMode();
                    });
                } else {
                    updateChildrenTotal();
                }

                const viewLeadModalEl = document.getElementById('viewLeadModal');
                const viewLeadLoader = document.getElementById('viewLeadLoader');
                const viewLeadContent = document.getElementById('viewLeadContent');
                const viewLeadAlert = document.getElementById('viewLeadAlert');
                const viewLeadMeta = document.getElementById('viewLeadMeta');
                const viewLeadStatus = document.getElementById('viewLeadStatus');
                const viewLeadTitle = document.getElementById('viewLeadModalLabel');
                const viewLeadService = document.getElementById('viewService');
                const viewLeadDestination = document.getElementById('viewDestination');
                const viewLeadTravelDate = document.getElementById('viewTravelDate');
                const viewLeadAssignedUser = document.getElementById('viewAssignedUser');
                const viewLeadRemarksContainer = document.getElementById('viewLeadRemarks');
                const viewLeadRemarksCount = document.getElementById('viewLeadRemarksCount');
                const remarkForm = document.getElementById('leadRemarkForm');

                let viewLeadModalInstance = null;
                let currentLeadId = null;
                // Global current lead data for use across view/edit flows
                let currentLeadData = null;

                // Function to show Bootstrap toast
                const showToast = (message, type = 'success') => {
                    const toastEl = document.getElementById('remarkToast');
                    const toastTitle = document.getElementById('remarkToastTitle');
                    const toastBody = document.getElementById('remarkToastBody');

                    if (!toastEl || !toastTitle || !toastBody) return;

                    // Set toast content
                    toastBody.textContent = message;

                    // Set toast header style based on type
                    const toastHeader = toastEl.querySelector('.toast-header');
                    const iconEl = toastHeader?.querySelector('i');

                    if (toastHeader) {
                        // Reset classes
                        toastHeader.className = 'toast-header';
                        if (type === 'success') {
                            toastHeader.classList.add('bg-success', 'text-white');
                            toastTitle.textContent = 'Success';
                            if (iconEl) {
                                iconEl.setAttribute('data-feather', 'check-circle');
                            }
                        } else {
                            toastHeader.classList.add('bg-danger', 'text-white');
                            toastTitle.textContent = 'Error';
                            if (iconEl) {
                                iconEl.setAttribute('data-feather', 'alert-circle');
                            }
                        }
                    }

                    // Initialize Feather icons
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }

                    // Show toast using Bootstrap
                    if (typeof bootstrap !== 'undefined') {
                        // Hide any existing toast first
                        const existingToast = bootstrap.Toast.getInstance(toastEl);
                        if (existingToast) {
                            existingToast.hide();
                        }

                        // Create and show new toast
                        const toast = new bootstrap.Toast(toastEl, {
                            autohide: true,
                            delay: type === 'success' ? 3000 : 5000
                        });
                        toast.show();
                    }
                };

                const escapeHtml = (unsafe) => {
                    if (unsafe === null || unsafe === undefined) {
                        return '';
                    }
                    return String(unsafe).replace(/[&<>"']/g, function(match) {
                        const map = {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;',
                        };
                        return map[match] || match;
                    });
                };

                const toggleText = (element, container, prefix, value) => {
                    if (!element) return;
                    if (value) {
                        element.textContent = prefix ? `${prefix} ${value}` : value;
                        if (container) {
                            container.classList.remove('d-none');
                        }
                        element.classList.remove('d-none');
                    } else {
                        element.textContent = '';
                        if (container) {
                            container.classList.add('d-none');
                        }
                        element.classList.add('d-none');
                    }
                };

                const renderPhoneNumbers = (primary, secondary, other) => {
                    const phones = [];
                    if (primary) phones.push({
                        number: primary,
                        label: 'Primary'
                    });
                    if (secondary) phones.push({
                        number: secondary,
                        label: 'Secondary'
                    });
                    if (other) phones.push({
                        number: other,
                        label: 'Other'
                    });

                    if (phones.length === 0) {
                        return '';
                    }

                    // Clean phone number for tel: link (remove spaces, dashes, etc.)
                    const cleanPhone = (phone) => {
                        return phone.replace(/[\s\-\(\)]/g, '');
                    };

                    return phones.map((phone, index) => {
                        const cleaned = cleanPhone(phone.number);
                        return `<a href="tel:${cleaned}" class="text-decoration-none text-primary fw-semibold">${escapeHtml(phone.number)}</a>${index < phones.length - 1 ? '<span class="text-muted">, </span>' : ''}`;
                    }).join('');
                };

                const renderTravelerBadges = (lead) => {
                    return `
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary text-white px-3 py-2">
                            <i data-feather="users" class="me-1" style="width: 14px; height: 14px;"></i>
                            Adults: ${lead.adults ?? 0}
                        </span>
                        <span class="badge bg-info text-white px-3 py-2">
                            <i data-feather="smile" class="me-1" style="width: 14px; height: 14px;"></i>
                            2-5 yrs: ${lead.children_2_5 ?? 0}
                        </span>
                        <span class="badge bg-success text-white px-3 py-2">
                            <i data-feather="smile" class="me-1" style="width: 14px; height: 14px;"></i>
                            6-11 yrs: ${lead.children_6_11 ?? 0}
                        </span>
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i data-feather="heart" class="me-1" style="width: 14px; height: 14px;"></i>
                            Infants: ${lead.infants ?? 0}
                        </span>
                    </div>
                `;
                };

                const renderRemarks = (remarks) => {
                    if (!remarks || !remarks.length) {
                        return '<p class="text-muted text-center mb-0 py-4"><i data-feather="message-circle" class="me-2" style="width: 16px; height: 16px;"></i>No remarks yet.</p>';
                    }

                    return remarks.map((remark, index) => {
                        return `
                        <div class="border rounded-3 p-3 mb-3 bg-white border">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-start flex-grow-1">
                                    <div class="avatar avatar-rounded rounded-circle me-3 flex-shrink-0" style="background-color: #007d88; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <span class="text-white fw-bold" style="font-size: 0.875rem;">
                                            ${escapeHtml((remark.user?.name ?? 'U')[0].toUpperCase())}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                            <strong class="text-dark">${escapeHtml(remark.user?.name ?? 'Unknown')}</strong>
                                        </div>
                                        <p class="mb-0 text-dark" style="line-height: 1.6;">${escapeHtml(remark.remark ?? '')}</p>
                                    </div>
                                </div>
                                <small class="text-muted flex-shrink-0 ms-2" style="white-space: nowrap;">${escapeHtml(remark.created_at ?? '')}</small>
                            </div>
                        </div>
                    `;
                    }).join('');
                };

                const resetViewLeadModal = () => {
                    if (viewLeadLoader) {
                        viewLeadLoader.classList.remove('d-none');
                    }
                    if (viewLeadContent) {
                        viewLeadContent.classList.add('d-none');
                    }
                    if (viewLeadAlert) {
                        viewLeadAlert.classList.add('d-none');
                        viewLeadAlert.textContent = '';
                        viewLeadAlert.classList.remove('alert-danger', 'alert-success');
                    }

                    if (remarkForm) {
                        remarkForm.reset();
                        remarkForm.dataset.leadId = '';
                    }

                    // Reset remarks container and counters
                    if (viewLeadRemarksContainer) {
                        viewLeadRemarksContainer.innerHTML =
                            '<p class="text-muted text-center mb-0 py-3">No remarks yet.</p>';
                    }
                    if (viewLeadRemarksCount) {
                        viewLeadRemarksCount.textContent = '0';
                    }
                    const viewLeadNextFollowUp = document.getElementById('viewLeadNextFollowUp');
                    if (viewLeadNextFollowUp) {
                        viewLeadNextFollowUp.classList.add('d-none');
                        viewLeadNextFollowUp.textContent = '';
                    }
                };

                const showViewLeadError = (message) => {
                    if (viewLeadAlert) {
                        viewLeadAlert.classList.remove('d-none');
                        viewLeadAlert.classList.remove('alert-success');
                        viewLeadAlert.classList.add('alert-danger');
                        viewLeadAlert.textContent = message;
                    }
                    if (viewLeadLoader) {
                        viewLeadLoader.classList.add('d-none');
                    }
                    if (viewLeadContent) {
                        viewLeadContent.classList.add('d-none');
                    }
                };

                const loadLeadDetails = async (leadId) => {
                    if (!leadId || !leadsBaseUrl) {
                        showViewLeadError('Invalid lead.');
                        return;
                    }

                    currentLeadId = leadId;
                    resetViewLeadModal();

                    try {
                        const response = await fetch(`${leadsBaseUrl}/${leadId}?modal=1`, {
                            headers: {
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Unable to load lead details.');
                        }

                        const data = await response.json();
                        const lead = data.lead;

                        if (!lead) {
                            throw new Error('Lead details not found.');
                        }

                        // Store lead data for editing
                        currentLeadData = lead;

                        // Pre-populate edit form fields if edit form exists
                        // const editRefNoEl = document.getElementById('editRefNo');
                        // if (editRefNoEl && lead.tsq) {
                        //     editRefNoEl.value = lead.tsq || 'N/A';
                        // }

                        if (viewLeadLoader) {
                            viewLeadLoader.classList.add('d-none');
                        }
                        if (viewLeadContent) {
                            viewLeadContent.classList.remove('d-none');
                        }

                        if (viewLeadTitle) {
                            viewLeadTitle.textContent = `${lead.tsq ?? 'Lead'} - ${lead.customer_name ?? ''}`;
                        }
                        if (viewLeadMeta) {
                            viewLeadMeta.textContent = lead.created_at ? `Created on ${lead.created_at}` : '';
                        }
                        if (viewLeadStatus) {
                            const statusClass = `badge ${lead.status_color ?? 'bg-secondary text-white'}`;
                            viewLeadStatus.className = statusClass;
                            viewLeadStatus.textContent = lead.status_label ?? lead.status ?? '-';
                        }
                        // Populate Customer Information
                        const viewRefNo = document.getElementById('viewRefNo');
                        const viewSalutation = document.getElementById('viewSalutation');
                        const viewFirstName = document.getElementById('viewFirstName');
                        const viewLastName = document.getElementById('viewLastName');
                        if (viewRefNo) viewRefNo.value = lead.tsq || 'N/A';
                        if (viewSalutation) viewSalutation.value = lead.salutation || '';
                        if (viewFirstName) viewFirstName.value = lead.first_name || '';
                        if (viewLastName) viewLastName.value = lead.last_name || '';

                        // Populate Contact Information
                        const viewPrimaryPhone = document.getElementById('viewPrimaryPhone');
                        const viewSecondaryPhone = document.getElementById('viewSecondaryPhone');
                        const viewOtherPhone = document.getElementById('viewOtherPhone');
                        const viewEmail = document.getElementById('viewEmail');
                        if (viewPrimaryPhone) viewPrimaryPhone.value = lead.primary_phone || '';
                        if (viewSecondaryPhone) viewSecondaryPhone.value = lead.secondary_phone || '';
                        if (viewOtherPhone) viewOtherPhone.value = lead.other_phone || '';
                        if (viewEmail) viewEmail.value = lead.email || '';

                        // Populate Address fields
                        const viewAddressLine = document.getElementById('viewAddressLine');
                        const viewCity = document.getElementById('viewCity');
                        const viewState = document.getElementById('viewState');
                        const viewCountry = document.getElementById('viewCountry');
                        const viewPinCode = document.getElementById('viewPinCode');
                        if (viewAddressLine) viewAddressLine.value = lead.address_line || '';
                        if (viewCity) viewCity.value = lead.city || '';
                        if (viewState) viewState.value = lead.state || '';
                        if (viewCountry) viewCountry.value = lead.country || '';
                        if (viewPinCode) viewPinCode.value = lead.pin_code || '';

                        // Populate Travel Preferences
                        if (viewLeadService) {
                            viewLeadService.value = lead.service ?? 'N/A';
                        }
                        if (viewLeadDestination) {
                            viewLeadDestination.value = lead.destination ?? 'N/A';
                        }
                        if (viewLeadTravelDate) {
                            viewLeadTravelDate.value = lead.travel_date ?? 'N/A';
                        }
                        const viewReturnDate = document.getElementById('viewReturnDate');
                        if (viewReturnDate) {
                            viewReturnDate.value = lead.return_date ?? 'N/A';
                        }
                        const viewAdults = document.getElementById('viewAdults');
                        const viewChildren25 = document.getElementById('viewChildren25');
                        const viewChildren611 = document.getElementById('viewChildren611');
                        const viewInfants = document.getElementById('viewInfants');
                        if (viewAdults) viewAdults.value = lead.adults ?? 0;
                        if (viewChildren25) viewChildren25.value = lead.children_2_5 ?? 0;
                        if (viewChildren611) viewChildren611.value = lead.children_6_11 ?? 0;
                        if (viewInfants) viewInfants.value = lead.infants ?? 0;

                        // Populate Assignment
                        if (viewLeadAssignedUser) {
                            viewLeadAssignedUser.value = lead.assigned_user ?? 'Unassigned';
                        }
                        const viewStatus = document.getElementById('viewStatus');
                        if (viewStatus) {
                            viewStatus.value = lead.status_label ?? lead.status ?? 'N/A';
                        }

                        if (viewLeadRemarksCount) {
                            viewLeadRemarksCount.textContent = data.remarks?.length ?? 0;
                        }
                        if (viewLeadRemarksContainer) {
                            viewLeadRemarksContainer.innerHTML = renderRemarks(data.remarks || []);
                        }

                        // Show next scheduled follow-up (nearest future follow_up_at)
                        const viewLeadNextFollowUp = document.getElementById('viewLeadNextFollowUp');
                        if (viewLeadNextFollowUp) {
                            if (data.next_follow_up) {
                                const nf = data.next_follow_up;
                                const label = nf.follow_up_date + (nf.follow_up_time ? ' ' + nf.follow_up_time :
                                    '');
                                viewLeadNextFollowUp.innerHTML =
                                    `<i data-feather="calendar" class="me-1" style="width: 12px; height: 12px;"></i> Follow-Up: ${label}`;
                                viewLeadNextFollowUp.classList.remove('d-none');
                                safeFeatherReplace(viewLeadNextFollowUp);
                            } else {
                                viewLeadNextFollowUp.classList.add('d-none');
                                viewLeadNextFollowUp.textContent = '';
                            }
                        }

                        if (remarkForm) {
                            remarkForm.dataset.leadId = lead.id;
                        }

                        // Initialize Feather icons after content is loaded
                        safeFeatherReplace(viewLeadContent);
                    } catch (error) {
                        console.error(error);
                        showViewLeadError(error.message || 'Unexpected error occurred.');
                    }
                };

                // Store loadLeadDetails on window for access in global event handler
                window.loadLeadDetails = loadLeadDetails;

                // Initialize modal instance first and store on window for access
                if (viewLeadModalEl && typeof bootstrap !== 'undefined') {
                    if (!viewLeadModalInstance) {
                        viewLeadModalInstance = new bootstrap.Modal(viewLeadModalEl, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        // Store on window for access in event handler
                        window.viewLeadModalInstance = viewLeadModalInstance;
                    }
                }

                // Use event delegation on document to catch clicks on dynamically created buttons
                // This must be outside the conditional to work with DataTable
                // Use a named function to prevent duplicate listeners
                if (!window.viewLeadClickHandler) {
                    window.viewLeadClickHandler = function(event) {
                        // Check if the clicked element or its parent has the view-lead-btn class
                        const button = event.target.closest('.view-lead-btn');
                        if (!button) {
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();

                        const leadId = button.dataset.leadId || button.getAttribute('data-lead-id');

                        if (!leadId) {
                            console.error('No lead ID found on button', button);
                            return;
                        }

                        // Get modal element
                        const modalEl = document.getElementById('viewLeadModal');
                        if (!modalEl) {
                            console.error('View lead modal element not found');
                            return;
                        }

                        // Get or create modal instance
                        let modalInstance = window.viewLeadModalInstance || viewLeadModalInstance;
                        if (!modalInstance && typeof bootstrap !== 'undefined') {
                            modalInstance = new bootstrap.Modal(modalEl, {
                                backdrop: 'static',
                                keyboard: false
                            });
                            window.viewLeadModalInstance = modalInstance;
                            viewLeadModalInstance = modalInstance;
                        }

                        if (modalInstance) {
                            modalInstance.show();
                            // Use window.loadLeadDetails which was stored from document.ready
                            if (typeof window.loadLeadDetails === 'function') {
                                window.loadLeadDetails(leadId);
                            } else {
                                console.error('loadLeadDetails function not found on window');
                            }
                        } else {
                            console.error('Bootstrap modal not available');
                        }
                    };

                    // Attach the event listener with capture phase
                    document.addEventListener('click', window.viewLeadClickHandler, true);
                }

                if (viewLeadModalEl) {
                    viewLeadModalEl.addEventListener('shown.bs.modal', () => {
                        // Initialize Feather icons when modal is shown
                        safeFeatherReplace(viewLeadModalEl);
                    });

                    viewLeadModalEl.addEventListener('hidden.bs.modal', () => {
                        currentLeadId = null;
                        currentLeadData = null;
                        resetViewLeadModal();
                        // Reset to view mode if in edit mode
                        const viewContent = document.getElementById('viewLeadContent');
                        const editContent = document.getElementById('editLeadContent');
                        const cancelBtn = document.getElementById('cancelEditBtn');
                        const modalTitle = document.getElementById('viewLeadModalTitle');
                        if (viewContent) viewContent.classList.remove('d-none');
                        if (editContent) editContent.classList.add('d-none');
                        if (cancelBtn) cancelBtn.classList.add('d-none');
                        if (modalTitle) modalTitle.textContent = 'Lead Details';
                    });
                }

                if (remarkForm) {
                    remarkForm.addEventListener('submit', async (event) => {
                        event.preventDefault();
                        if (!currentLeadId || !leadsBaseUrl) {
                            return;
                        }



                        const formData = new FormData(remarkForm);

                        try {
                            const response = await fetch(`${leadsBaseUrl}/${currentLeadId}/remarks`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                },
                                body: formData,
                            });

                            const payload = await response.json();

                            if (!response.ok) {
                                const message = payload?.message || Object.values(payload?.errors || {})[0]
                                    ?.[0] || 'Failed to add remark.';
                                throw new Error(message);
                            }

                            // Show success toast
                            showToast(payload?.message || 'Remark added successfully!', 'success');


                            remarkForm.reset();



                            // Add new remark to the list without reloading the entire modal
                            if (payload?.remark && viewLeadRemarksContainer) {
                                // Get current remarks from the container
                                const currentRemarksHtml = viewLeadRemarksContainer.innerHTML;
                                const isEmpty = currentRemarksHtml.includes('No remarks yet');

                                // Render the new remark (it should be first in the list)
                                const newRemarkHtml = renderRemarks([payload.remark]);

                                if (isEmpty) {
                                    // If no remarks existed, replace the empty message
                                    viewLeadRemarksContainer.innerHTML = newRemarkHtml;
                                } else {
                                    // Prepend the new remark to the existing list
                                    viewLeadRemarksContainer.innerHTML = newRemarkHtml + currentRemarksHtml;
                                }

                                // Update remark count
                                if (viewLeadRemarksCount) {
                                    const currentCount = parseInt(viewLeadRemarksCount.textContent) || 0;
                                    viewLeadRemarksCount.textContent = currentCount + 1;
                                }

                                // Update next follow-up badge if this remark schedules a future follow-up
                                if (payload?.remark?.follow_up_at) {
                                    try {
                                        // Normalize to ISO for Date parsing
                                        const followUpIso = payload.remark.follow_up_at.replace(' ', 'T');
                                        const followUpDate = new Date(followUpIso);
                                        const now = new Date();
                                        if (followUpDate > now) {
                                            const viewLeadNextFollowUpEl = document.getElementById(
                                                'viewLeadNextFollowUp');
                                            if (viewLeadNextFollowUpEl) {
                                                const label = payload.remark.follow_up_date + (payload
                                                    .remark.follow_up_time ? ' ' + payload.remark
                                                    .follow_up_time : '');
                                                viewLeadNextFollowUpEl.innerHTML =
                                                    `<i data-feather="calendar" class="me-1" style="width: 12px; height: 12px;"></i> Follow-Up: ${label}`;
                                                viewLeadNextFollowUpEl.classList.remove('d-none');
                                                safeFeatherReplace(viewLeadNextFollowUpEl);
                                            }
                                        }
                                    } catch (e) {
                                        // ignore parsing errors
                                    }
                                }

                                // Initialize Feather icons for the new remark
                                safeFeatherReplace(viewLeadRemarksContainer);
                            }
                        } catch (error) {
                            // Show error toast
                            showToast(error.message || 'Unable to add remark.', 'error');


                        }
                    });
                }

                // Edit Lead Functionality
                const cancelEditBtn = document.getElementById('cancelEditBtn');
                const cancelEditFormBtn = document.getElementById('cancelEditFormBtn');
                const editLeadContent = document.getElementById('editLeadContent');
                const editLeadForm = document.getElementById('editLeadForm');
                const editLeadAlert = document.getElementById('editLeadAlert');
                const viewLeadModalTitle = document.getElementById('viewLeadModalTitle');
                let currentEditLeadId = null;

                // Function to update children total for edit form
                const updateEditChildrenTotal = () => {
                    const editLeadForm = document.getElementById('editLeadForm');
                    if (!editLeadForm) return;
                    const child2_5 = parseInt(editLeadForm.elements['children_2_5']?.value || '0', 10);
                    const child6_11 = parseInt(editLeadForm.elements['children_6_11']?.value || '0', 10);
                    const editChildrenTotal = document.getElementById('editChildrenTotal');
                    if (editChildrenTotal) {
                        editChildrenTotal.value = (child2_5 || 0) + (child6_11 || 0);
                    }
                };

                // Function to populate edit form with lead data
                const populateEditForm = (lead) => {
                    if (!lead) return;

                    const editRefNoEl = document.getElementById('editRefNo');
                    if (editRefNoEl) {
                        editRefNoEl.value = lead.tsq || 'N/A';
                    }

                    const editSalutationEl = document.getElementById('editSalutation');
                    if (editSalutationEl) {
                        editSalutationEl.value = lead.salutation || '';
                    }

                    document.getElementById('editFirstName').value = lead.first_name || '';
                    document.getElementById('editLastName').value = lead.last_name || '';
                    document.getElementById('editPrimaryPhone').value = lead.primary_phone || '';
                    document.getElementById('editSecondaryPhone').value = lead.secondary_phone || '';
                    document.getElementById('editOtherPhone').value = lead.other_phone || '';
                    document.getElementById('editEmail').value = lead.email || '';
                    document.getElementById('editAddressLine').value = lead.address_line || '';
                    document.getElementById('editCity').value = lead.city || '';
                    document.getElementById('editState').value = lead.state || '';
                    document.getElementById('editCountry').value = lead.country || '';
                    document.getElementById('editPinCode').value = lead.pin_code || '';
                    document.getElementById('editServiceId').value = lead.service_id || '';
                    document.getElementById('editDestinationId').value = lead.destination_id || '';
                    document.getElementById('editTravelDate').value = lead.travel_date_raw || '';
                    document.getElementById('editReturnDate').value = lead.return_date_raw || '';
                    document.getElementById('editAdults').value = lead.adults || 0;
                    document.getElementById('editChildren25').value = lead.children_2_5 || 0;
                    document.getElementById('editChildren611').value = lead.children_6_11 || 0;
                    document.getElementById('editInfants').value = lead.infants || 0;

                    // Map assigned_user_id to employee_id for the dropdown
                    let assignedEmployeeId = '';
                    if (lead.assigned_user_id) {
                        const employeeSelect = document.getElementById('editAssignedEmployeeId');
                        if (employeeSelect) {
                            // Find option with matching data-user-id attribute
                            const options = employeeSelect.querySelectorAll('option');
                            options.forEach(option => {
                                if (option.getAttribute('data-user-id') == lead.assigned_user_id) {
                                    assignedEmployeeId = option.value;
                                }
                            });
                            // Fallback to email match
                            if (!assignedEmployeeId && lead.assigned_user_email) {
                                options.forEach(option => {
                                    if (option.getAttribute('data-user-email') == lead
                                        .assigned_user_email) {
                                        assignedEmployeeId = option.value;
                                    }
                                });
                            }
                        }
                    }
                    const editAssignedEmployeeId = document.getElementById('editAssignedEmployeeId');
                    if (editAssignedEmployeeId) {
                        editAssignedEmployeeId.value = assignedEmployeeId || '';
                    }
                    document.getElementById('editStatus').value = lead.status || 'new';

                    // Update children total
                    updateEditChildrenTotal();
                };

                // Function to switch to edit mode
                const switchToEditMode = () => {
                    if (viewLeadContent) viewLeadContent.classList.add('d-none');
                    if (editLeadContent) editLeadContent.classList.remove('d-none');
                    if (cancelEditBtn) cancelEditBtn.classList.remove('d-none');
                    if (viewLeadModalTitle) viewLeadModalTitle.textContent = 'Edit Lead';

                    // Populate edit form with current lead data
                    if (currentLeadData) {
                        populateEditForm(currentLeadData);
                    }

                    if (typeof feather !== 'undefined') feather.replace();
                };

                // Function to switch back to view mode
                const switchToViewMode = () => {
                    if (viewLeadContent) viewLeadContent.classList.remove('d-none');
                    if (editLeadContent) editLeadContent.classList.add('d-none');
                    if (cancelEditBtn) cancelEditBtn.classList.add('d-none');
                    if (viewLeadModalTitle) viewLeadModalTitle.textContent = 'Lead Details';
                    if (editLeadAlert) {
                        editLeadAlert.classList.add('d-none');
                        editLeadAlert.textContent = '';
                    }
                };

                // Function to populate Add Lead form for editing
                const populateAddLeadFormForEdit = (lead) => {
                    if (!lead || !addLeadForm) return;

                    // Set form mode to edit
                    const formMethod = document.getElementById('formMethod');
                    const editLeadId = document.getElementById('editLeadId');
                    const modalLabel = document.getElementById('addLeadModalLabel');
                    const submitBtn = document.getElementById('addLeadSubmitBtn');

                    if (formMethod) formMethod.value = 'PUT';
                    if (editLeadId) editLeadId.value = lead.id;
                    if (modalLabel) modalLabel.textContent = 'Edit Lead';
                    if (submitBtn) submitBtn.textContent = 'Update Lead';

                    // Update form action for direct form submission
                    addLeadForm.action = `${leadsBaseUrl}/${lead.id}`;

                    // Populate form fields
                    const addRefNoEl = document.getElementById('addRefNo');
                    if (addRefNoEl) addRefNoEl.value = lead.tsq || 'N/A';
                    if (addLeadForm.elements['salutation']) addLeadForm.elements['salutation'].value = lead
                        .salutation || '';
                    if (addLeadForm.elements['first_name']) addLeadForm.elements['first_name'].value = lead
                        .first_name || '';
                    if (addLeadForm.elements['last_name']) addLeadForm.elements['last_name'].value = lead
                        .last_name || '';
                    if (addLeadForm.elements['primary_phone']) addLeadForm.elements['primary_phone'].value = lead
                        .primary_phone || '';
                    if (addLeadForm.elements['secondary_phone']) addLeadForm.elements['secondary_phone'].value =
                        lead.secondary_phone || '';
                    if (addLeadForm.elements['other_phone']) addLeadForm.elements['other_phone'].value = lead
                        .other_phone || '';
                    if (addLeadForm.elements['email']) addLeadForm.elements['email'].value = lead.email || '';
                    if (addLeadForm.elements['address_line']) addLeadForm.elements['address_line'].value = lead
                        .address_line || '';
                    if (addLeadForm.elements['city']) addLeadForm.elements['city'].value = lead.city || '';
                    if (addLeadForm.elements['state']) addLeadForm.elements['state'].value = lead.state || '';
                    if (addLeadForm.elements['country']) addLeadForm.elements['country'].value = lead.country || '';
                    if (addLeadForm.elements['pin_code']) addLeadForm.elements['pin_code'].value = lead.pin_code ||
                        '';
                    if (addLeadForm.elements['service_id']) addLeadForm.elements['service_id'].value = lead
                        .service_id || '';
                    if (addLeadForm.elements['destination_id']) addLeadForm.elements['destination_id'].value = lead
                        .destination_id || '';
                    if (addLeadForm.elements['travel_date']) {
                        const travelDateInput = addLeadForm.elements['travel_date'];
                        travelDateInput.value = lead.travel_date_raw || '';
                    }
                    if (addLeadForm.elements['return_date']) {
                        const returnDateInput = addLeadForm.elements['return_date'];
                        returnDateInput.value = lead.return_date_raw || '';
                    }
                    if (addLeadForm.elements['adults']) addLeadForm.elements['adults'].value = lead.adults || 0;
                    if (addLeadForm.elements['children_2_5']) addLeadForm.elements['children_2_5'].value = lead
                        .children_2_5 || 0;
                    if (addLeadForm.elements['children_6_11']) addLeadForm.elements['children_6_11'].value = lead
                        .children_6_11 || 0;
                    if (addLeadForm.elements['infants']) addLeadForm.elements['infants'].value = lead.infants || 0;
                    // Map assigned_user_id
                    if (addLeadForm.elements['assigned_user_id']) {
                        const employeeSelect = addLeadForm.elements['assigned_user_id'];
                        let foundEmployeeId = '';

                        // Priority 1: Direct value match
                        if (lead.assigned_user_id) {
                            foundEmployeeId = lead.assigned_user_id;
                        }

                        // Priority 2: Try matching by data-user-id if direct value doesn't seem to exist in options
                        // (Though setting .value directly works for standard selects if the value exists)
                        if (foundEmployeeId) {
                            employeeSelect.value = foundEmployeeId;

                            // If it wasn't selected (value is empty or different), try data-user-id mapping
                            if (employeeSelect.value != foundEmployeeId) {
                                const options = employeeSelect.querySelectorAll('option');
                                options.forEach(option => {
                                    if (option.getAttribute('data-user-id') == lead.assigned_user_id) {
                                        foundEmployeeId = option.value;
                                    }
                                });
                                employeeSelect.value = foundEmployeeId || '';
                            }
                        }

                        // Final Fallback: try matching by employee email if still not found
                        if (!employeeSelect.value && lead.assigned_user_email) {
                            const options = employeeSelect.querySelectorAll('option');
                            options.forEach(option => {
                                if (option.getAttribute('data-user-email') == lead.assigned_user_email) {
                                    employeeSelect.value = option.value;
                                }
                            });
                        }
                    }
                    if (addLeadForm.elements['status']) addLeadForm.elements['status'].value = lead.status || 'new';

                    // Update children total
                    updateChildrenTotal();
                };

                // Function to reset form to add mode
                const resetAddLeadFormToAddMode = () => {
                    if (!addLeadForm) return;

                    const formMethod = document.getElementById('formMethod');
                    const editLeadId = document.getElementById('editLeadId');
                    const modalLabel = document.getElementById('addLeadModalLabel');
                    const submitBtn = document.getElementById('addLeadSubmitBtn');

                    if (formMethod) formMethod.value = 'POST';
                    if (editLeadId) editLeadId.value = '';
                    if (modalLabel) modalLabel.textContent = 'Add Lead';
                    if (submitBtn) submitBtn.textContent = 'Add Lead';

                    // Reset form action
                    addLeadForm.action = '{{ route($storeRoute ?? 'leads.store') }}';

                    // Reset form
                    addLeadForm.reset();
                    // Clear displayed Ref No when switching back to Add mode
                    const addRefNoEl = document.getElementById('addRefNo');
                    if (addRefNoEl) addRefNoEl.value = '';
                    updateChildrenTotal();
                };

                // Edit button click handler (from table) - opens Add Lead modal in edit mode
                if (!window.editLeadClickHandler) {
                    window.editLeadClickHandler = function(event) {
                        const button = event.target.closest('.edit-lead-btn');
                        if (!button) {
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();

                        const leadId = button.dataset.leadId || button.getAttribute('data-lead-id');

                        if (!leadId) {
                            console.error('No lead ID found on edit button', button);
                            return;
                        }

                        // Fetch lead data and open Add Lead modal in edit mode
                        fetch(`${leadsBaseUrl}/${leadId}?modal=1`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.lead) {
                                    // Open Add Lead modal
                                    if (addLeadModalEl && typeof bootstrap !== 'undefined') {
                                        const modalInstance = bootstrap.Modal.getOrCreateInstance(
                                            addLeadModalEl);
                                        populateAddLeadFormForEdit(data.lead);
                                        modalInstance.show();
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error loading lead for edit:', error);
                                alert('Unable to load lead details for editing.');
                            });
                    };
                    document.addEventListener('click', window.editLeadClickHandler);
                }

                // Cancel edit button handlers
                if (cancelEditBtn) {
                    cancelEditBtn.addEventListener('click', () => {
                        switchToViewMode();
                    });
                }

                if (cancelEditFormBtn) {
                    cancelEditFormBtn.addEventListener('click', () => {
                        switchToViewMode();
                    });
                }

                // Add event listeners for children fields in edit form
                if (editLeadForm) {
                    editLeadForm.querySelectorAll('input[name="children_2_5"], input[name="children_6_11"]').forEach((
                        field) => {
                        field.addEventListener('input', updateEditChildrenTotal);
                        field.addEventListener('change', updateEditChildrenTotal);
                    });
                }

                // Edit form submission
                if (editLeadForm) {
                    editLeadForm.addEventListener('submit', async (event) => {
                        event.preventDefault();
                        if (!currentEditLeadId || !leadsBaseUrl) {
                            return;
                        }

                        // Update children total before submission
                        updateEditChildrenTotal();

                        if (editLeadAlert) {
                            editLeadAlert.classList.add('d-none');
                            editLeadAlert.classList.remove('alert-danger', 'alert-success');
                        }

                        try {
                            const formData = new FormData(editLeadForm);
                            // Ensure _method is set for Laravel method spoofing
                            formData.append('_method', 'PUT');
                            const response = await fetch(`${leadsBaseUrl}/${currentEditLeadId}`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                },
                                body: formData,
                            });

                            const payload = await response.json();

                            if (!response.ok) {
                                const message = payload?.message || Object.values(payload?.errors || {})[0]
                                    ?.[0] || 'Failed to update lead.';
                                throw new Error(message);
                            }

                            if (editLeadAlert) {
                                editLeadAlert.classList.remove('d-none');
                                editLeadAlert.classList.add('alert-success');
                                editLeadAlert.textContent = payload?.message ||
                                    'Lead updated successfully!';
                            }

                            // Reload lead details and switch back to view mode
                            setTimeout(() => {
                                if (typeof window.loadLeadDetails === 'function') {
                                    window.loadLeadDetails(currentEditLeadId);
                                }
                                switchToViewMode();
                            }, 1000);
                        } catch (error) {
                            if (editLeadAlert) {
                                editLeadAlert.classList.remove('d-none');
                                editLeadAlert.classList.add('alert-danger');
                                editLeadAlert.textContent = error.message || 'Unable to update lead.';
                            }
                        }
                    });
                }


                // Assign User Modal
                const assignUserModalEl = document.getElementById('assignUserModal');
                const assignUserButtons = document.querySelectorAll('.assign-user-btn');
                const assignUserLeadName = document.getElementById('assignUserLeadName');
                const assignUserCurrentUser = document.getElementById('assignUserCurrentUser');
                const assignUserSelect = document.getElementById('assignUserSelect');
                const assignUserSubmitBtn = document.getElementById('assignUserSubmitBtn');
                const assignUserAlert = document.getElementById('assignUserAlert');
                let assignUserModalInstance = null;
                let currentAssignLeadId = null;

                if (assignUserModalEl && typeof bootstrap !== 'undefined') {
                    assignUserModalInstance = new bootstrap.Modal(assignUserModalEl);
                }

                // Handle assign user button clicks
                document.addEventListener('click', function(event) {
                    const button = event.target.closest('.assign-user-btn');
                    if (!button) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    const leadId = button.dataset.leadId || button.getAttribute('data-lead-id');
                    const leadName = button.dataset.leadName || button.getAttribute('data-lead-name');
                    const currentUser = button.dataset.currentUser || button.getAttribute('data-current-user');

                    if (!leadId) {
                        console.error('No lead ID found on button');
                        return;
                    }

                    currentAssignLeadId = leadId;

                    if (assignUserLeadName) {
                        assignUserLeadName.value = leadName || 'N/A';
                    }
                    if (assignUserCurrentUser) {
                        assignUserCurrentUser.value = currentUser || 'Unassigned';
                    }
                    if (assignUserSelect) {
                        assignUserSelect.value = '';
                    }
                    if (assignUserAlert) {
                        assignUserAlert.classList.add('d-none');
                        assignUserAlert.textContent = '';
                    }

                    if (assignUserModalInstance) {
                        assignUserModalInstance.show();
                    }
                });

                // Handle assign user form submission
                if (assignUserSubmitBtn) {
                    assignUserSubmitBtn.addEventListener('click', async function() {
                        if (!currentAssignLeadId || !assignUserSelect || !leadsBaseUrl) {
                            return;
                        }

                        const employeeId = assignUserSelect.value;
                        if (!employeeId) {
                            if (assignUserAlert) {
                                assignUserAlert.classList.remove('d-none');
                                assignUserAlert.classList.remove('alert-success');
                                assignUserAlert.classList.add('alert-danger');
                                assignUserAlert.textContent = 'Please select an employee to assign.';
                            }
                            return;
                        }

                        if (assignUserAlert) {
                            assignUserAlert.classList.add('d-none');
                            assignUserAlert.classList.remove('alert-danger', 'alert-success');
                        }

                        try {
                            const response = await fetch(
                                `${leadsBaseUrl}/${currentAssignLeadId}/assign-user`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]')?.getAttribute('content') ||
                                            ''
                                    },
                                    body: JSON.stringify({
                                        assigned_user_id: employeeId
                                    })
                                });

                            const payload = await response.json();

                            if (!response.ok) {
                                const message = payload?.message || Object.values(payload?.errors || {})[0]
                                    ?.[0] || 'Failed to assign user.';
                                throw new Error(message);
                            }

                            if (assignUserAlert) {
                                assignUserAlert.classList.remove('d-none');
                                assignUserAlert.classList.add('alert-success');
                                assignUserAlert.textContent = payload?.message ||
                                    'User assigned successfully!';
                            }

                            // Close modal after a short delay
                            setTimeout(() => {
                                if (assignUserModalInstance) {
                                    assignUserModalInstance.hide();
                                }
                                // Reload page to update the table
                                window.location.reload();
                            }, 1000);
                        } catch (error) {
                            if (assignUserAlert) {
                                assignUserAlert.classList.remove('d-none');
                                assignUserAlert.classList.add('alert-danger');
                                assignUserAlert.textContent = error.message || 'Unable to assign user.';
                            }
                        }
                    });
                }

                // Initialize Feather icons when assign modal is shown
                if (assignUserModalEl) {
                    assignUserModalEl.addEventListener('shown.bs.modal', () => {
                        safeFeatherReplace(assignUserModalEl);
                    });
                }

                // ========== Bulk Assign Functionality ==========
                const selectAllLeads = document.getElementById('selectAllLeads');
                const leadCheckboxes = document.querySelectorAll('.lead-checkbox');
                const bulkActionsBar = document.getElementById('bulkActionsBar');
                const selectedCount = document.getElementById('selectedCount');
                const clearSelectionBtn = document.getElementById('clearSelectionBtn');
                const bulkAssignBtn = document.getElementById('bulkAssignBtn');
                const bulkAssignModal = document.getElementById('bulkAssignModal');
                const bulkAssignUserSelect = document.getElementById('bulkAssignUserSelect');
                const bulkAssignSubmitBtn = document.getElementById('bulkAssignSubmitBtn');
                const bulkAssignAlert = document.getElementById('bulkAssignAlert');
                const bulkAssignSelectedLeads = document.getElementById('bulkAssignSelectedLeads');

                // Function to update selected count and show/hide bulk actions bar
                function updateBulkActions() {
                    const checkedBoxes = document.querySelectorAll('.lead-checkbox:checked');
                    const count = checkedBoxes.length;

                    if (selectedCount) {
                        selectedCount.textContent = count;
                    }

                    if (bulkActionsBar) {
                        if (count > 0) {
                            bulkActionsBar.classList.remove('d-none');
                        } else {
                            bulkActionsBar.classList.add('d-none');
                        }
                    }

                    // Update select all checkbox state
                    if (selectAllLeads && leadCheckboxes.length > 0) {
                        selectAllLeads.checked = count === leadCheckboxes.length;
                        selectAllLeads.indeterminate = count > 0 && count < leadCheckboxes.length;
                    }

                    // Update selected leads display in modal
                    if (bulkAssignSelectedLeads) {
                        if (count > 0) {
                            const selectedNames = Array.from(checkedBoxes).map(cb => {
                                return cb.getAttribute('data-lead-name') || 'Lead #' + cb.value;
                            });
                            bulkAssignSelectedLeads.innerHTML = selectedNames.map(name =>
                                `<div class="badge bg-primary me-1 mb-1">${name}</div>`
                            ).join('');
                        } else {
                            bulkAssignSelectedLeads.innerHTML = '<small class="text-muted">No leads selected</small>';
                        }
                    }
                }

                // Select all checkbox handler
                if (selectAllLeads) {
                    selectAllLeads.addEventListener('change', function() {
                        leadCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateBulkActions();
                    });
                }

                // Individual checkbox handlers
                leadCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateBulkActions);
                });

                // Clear selection button
                if (clearSelectionBtn) {
                    clearSelectionBtn.addEventListener('click', function() {
                        leadCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        if (selectAllLeads) {
                            selectAllLeads.checked = false;
                            selectAllLeads.indeterminate = false;
                        }
                        updateBulkActions();
                    });
                }

                // Bulk assign form submission
                if (bulkAssignSubmitBtn) {
                    bulkAssignSubmitBtn.addEventListener('click', async function() {
                        const checkedBoxes = document.querySelectorAll('.lead-checkbox:checked');
                        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

                        if (selectedIds.length === 0) {
                            if (bulkAssignAlert) {
                                bulkAssignAlert.classList.remove('d-none', 'alert-success');
                                bulkAssignAlert.classList.add('alert-danger');
                                bulkAssignAlert.textContent = 'Please select at least one lead.';
                            }
                            return;
                        }

                        const employeeId = bulkAssignUserSelect?.value;
                        if (!employeeId) {
                            if (bulkAssignAlert) {
                                bulkAssignAlert.classList.remove('d-none', 'alert-success');
                                bulkAssignAlert.classList.add('alert-danger');
                                bulkAssignAlert.textContent = 'Please select an employee to assign.';
                            }
                            return;
                        }

                        if (bulkAssignAlert) {
                            bulkAssignAlert.classList.add('d-none');
                            bulkAssignAlert.classList.remove('alert-danger', 'alert-success');
                        }

                        // Disable button during request
                        bulkAssignSubmitBtn.disabled = true;
                        bulkAssignSubmitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-1"></span>Assigning...';

                        try {
                            const response = await fetch(
                                '{{ route($bulkAssignRoute ?? 'leads.bulkAssign') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]')?.getAttribute('content') ||
                                            ''
                                    },
                                    body: JSON.stringify({
                                        lead_ids: selectedIds,
                                        assigned_user_id: employeeId
                                    })
                                });

                            const payload = await response.json();

                            if (!response.ok) {
                                const message = payload?.message || Object.values(payload?.errors || {})[0]
                                    ?.[0] || 'Failed to assign leads.';
                                throw new Error(message);
                            }

                            if (bulkAssignAlert) {
                                bulkAssignAlert.classList.remove('d-none');
                                bulkAssignAlert.classList.add('alert-success');
                                bulkAssignAlert.textContent = payload?.message ||
                                    `Successfully assigned ${selectedIds.length} lead(s).`;
                            }

                            // Close modal after 1.5 seconds and reload page
                            setTimeout(() => {
                                if (bulkAssignModal) {
                                    const modalInstance = bootstrap.Modal.getInstance(
                                        bulkAssignModal);
                                    if (modalInstance) {
                                        modalInstance.hide();
                                    }
                                }
                                // Clear selection
                                leadCheckboxes.forEach(checkbox => {
                                    checkbox.checked = false;
                                });
                                if (selectAllLeads) {
                                    selectAllLeads.checked = false;
                                    selectAllLeads.indeterminate = false;
                                }
                                updateBulkActions();
                                // Reload page to show updated assignments
                                window.location.reload();
                            }, 1500);

                        } catch (error) {
                            if (bulkAssignAlert) {
                                bulkAssignAlert.classList.remove('d-none');
                                bulkAssignAlert.classList.add('alert-danger');
                                bulkAssignAlert.textContent = error.message || 'Unable to assign leads.';
                            }
                        } finally {
                            // Re-enable button
                            bulkAssignSubmitBtn.disabled = false;
                            bulkAssignSubmitBtn.innerHTML =
                                '<i data-feather="check" class="me-1" style="width: 16px; height: 16px;"></i>Assign Selected Leads';
                            safeFeatherReplace(bulkAssignSubmitBtn);
                        }
                    });
                }

                // Initialize bulk actions on page load
                updateBulkActions();

                // Initialize Feather icons when bulk assign modal is shown
                if (bulkAssignModal) {
                    bulkAssignModal.addEventListener('shown.bs.modal', () => {
                        safeFeatherReplace(bulkAssignModal);
                        // Update selected leads display
                        updateBulkActions();
                    });
                }

                // Send SMS Functionality
                const sendSmsModalEl = document.getElementById('sendSmsModal');
                const sendSmsForm = document.getElementById('sendSmsForm');
                const phoneNumbersList = document.getElementById('phoneNumbersList');
                const selectAllNumbers = document.getElementById('selectAllNumbers');
                const sendSmsSubmitBtn = document.getElementById('sendSmsSubmitBtn');
                const messageTemplatesList = document.getElementById('messageTemplatesList');
                let sendSmsModalInstance = null;
                let currentLeadIdForSms = null;

                if (sendSmsModalEl && typeof bootstrap !== 'undefined') {
                    sendSmsModalInstance = new bootstrap.Modal(sendSmsModalEl);

                    sendSmsModalEl.addEventListener('hidden.bs.modal', () => {
                        if (sendSmsForm) sendSmsForm.reset();
                        if (phoneNumbersList) phoneNumbersList.innerHTML = '';
                        if (selectAllNumbers) selectAllNumbers.checked = false;
                        currentLeadIdForSms = null;
                    });

                    sendSmsModalEl.addEventListener('shown.bs.modal', () => {
                        safeFeatherReplace(sendSmsModalEl);
                        // Load templates when modal is shown
                        loadSmsTemplates();
                    });
                }

                // Load SMS templates from API
                function loadSmsTemplates() {
                    if (!messageTemplatesList) return;

                    fetch('{{ route('leads.sms.templates') }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.templates) {
                                messageTemplatesList.innerHTML = data.templates.map((template, index) => `
                                    <label class="list-group-item">
                                        <input class="form-check-input me-1" type="radio" name="messageTemplate" 
                                            value="${template.key}" ${index === 0 ? 'checked' : ''}>
                                        <strong>${escapeHtml(template.name)}:</strong> ${escapeHtml(template.message)}
                                    </label>
                                `).join('');
                            }
                        })
                        .catch(error => {
                            console.error('Error loading SMS templates:', error);
                            messageTemplatesList.innerHTML =
                                '<p class="text-danger small mb-0">Failed to load templates</p>';
                        });
                }

                // Event delegation for send SMS button
                document.addEventListener('click', function(event) {
                    const button = event.target.closest('.send-sms-btn');
                    if (!button) return;

                    event.preventDefault();
                    event.stopPropagation();

                    const leadId = button.dataset.leadId;
                    const phone = button.dataset.phone;
                    const secondaryPhone = button.dataset.secondaryPhone;
                    const otherPhone = button.dataset.otherPhone;

                    currentLeadIdForSms = leadId;

                    // Populate numbers
                    let numbers = [];
                    if (phone && phone !== 'null') numbers.push({
                        label: 'Primary',
                        number: phone
                    });
                    if (secondaryPhone && secondaryPhone !== 'null') numbers.push({
                        label: 'Secondary',
                        number: secondaryPhone
                    });
                    if (otherPhone && otherPhone !== 'null') numbers.push({
                        label: 'Emergency',
                        number: otherPhone
                    });

                    if (phoneNumbersList) {
                        if (numbers.length > 0) {
                            phoneNumbersList.innerHTML = numbers.map((n, index) => `
                        <div class="form-check mb-1">
                            <input class="form-check-input phone-number-checkbox" type="checkbox" name="phone_numbers[]" value="${n.number}" id="phone_${index}">
                            <label class="form-check-label" for="phone_${index}">
                                ${n.number} <span class="text-muted small">(${n.label})</span>
                            </label>
                        </div>
                    `).join('');
                        } else {
                            phoneNumbersList.innerHTML =
                                '<p class="text-muted small mb-0">No phone numbers found.</p>';
                        }
                    }

                    if (sendSmsModalInstance) {
                        sendSmsModalInstance.show();
                    }
                });

                if (selectAllNumbers) {
                    selectAllNumbers.addEventListener('change', function() {
                        const checkboxes = document.querySelectorAll('.phone-number-checkbox');
                        checkboxes.forEach(cb => cb.checked = this.checked);
                    });
                }

                if (sendSmsSubmitBtn) {
                    sendSmsSubmitBtn.addEventListener('click', function() {
                        const selectedNumbers = Array.from(document.querySelectorAll(
                            '.phone-number-checkbox:checked')).map(cb => cb.value);
                        const selectedTemplate = document.querySelector(
                            'input[name="messageTemplate"]:checked')?.value;

                        if (selectedNumbers.length === 0) {
                            if (typeof showToast === 'function') {
                                showToast('Please select at least one phone number.', 'error');
                            } else {
                                alert('Please select at least one phone number.');
                            }
                            return;
                        }

                        if (!selectedTemplate) {
                            if (typeof showToast === 'function') {
                                showToast('Please select a message template.', 'error');
                            } else {
                                alert('Please select a message template.');
                            }
                            return;
                        }

                        if (!currentLeadIdForSms) {
                            if (typeof showToast === 'function') {
                                showToast('Lead ID not found.', 'error');
                            } else {
                                alert('Lead ID not found.');
                            }
                            return;
                        }

                        // Disable button and show loading
                        sendSmsSubmitBtn.disabled = true;
                        const originalText = sendSmsSubmitBtn.innerHTML;
                        sendSmsSubmitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

                        // Send SMS via API
                        fetch('{{ route('leads.sms.send') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        ?.content || ''
                                },
                                body: JSON.stringify({
                                    lead_id: currentLeadIdForSms,
                                    phone_numbers: selectedNumbers,
                                    template_key: selectedTemplate
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Re-enable button
                                sendSmsSubmitBtn.disabled = false;
                                sendSmsSubmitBtn.innerHTML = originalText;

                                if (data.success) {
                                    // Close modal
                                    if (sendSmsModalInstance) {
                                        sendSmsModalInstance.hide();
                                    }

                                    // Show success message
                                    if (typeof showToast === 'function') {
                                        showToast('SMS sent successfully!', 'success');
                                    } else {
                                        alert('SMS sent successfully!');
                                    }
                                } else {
                                    // Show error message
                                    if (typeof showToast === 'function') {
                                        showToast(data.message || 'Failed to send SMS', 'error');
                                    } else {
                                        alert(data.message || 'Failed to send SMS');
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error sending SMS:', error);

                                // Re-enable button
                                sendSmsSubmitBtn.disabled = false;
                                sendSmsSubmitBtn.innerHTML = originalText;

                                if (typeof showToast === 'function') {
                                    showToast('Failed to send SMS. Please try again.', 'error');
                                } else {
                                    alert('Failed to send SMS. Please try again.');
                                }
                            });
                    });
                }

            });
        </script>
    @endpush

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalElement = document.getElementById('addLeadModal');
                if (modalElement && typeof bootstrap !== 'undefined') {
                    const addLeadModal = new bootstrap.Modal(modalElement);
                    addLeadModal.show();
                }
            });
        </script>
    @endif
@endsection
