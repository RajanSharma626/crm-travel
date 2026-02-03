@extends('layouts.app')
@section('title', 'Accounts | Travel Shravel')
@section('content')
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

                                <form method="GET" action="{{ route('accounts.index') }}" class="row g-3 mb-4"
                                    id="accountsFiltersForm">
                                    <div class="col-md-3 col-lg-2">
                                        <label for="travel_date" class="form-label">Travel Date</label>
                                        <input type="date" name="travel_date" id="travel_date"
                                            class="form-control form-control-sm"
                                            value="{{ $filters['travel_date'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="next_days" class="form-label">Next Days</label>
                                        <select name="next_days" id="next_days" class="form-select form-select-sm">
                                            <option value="">-- Select --</option>
                                            <option value="7"
                                                {{ isset($filters['next_days']) && $filters['next_days'] == '7' ? 'selected' : '' }}>
                                                7 Days</option>
                                            <option value="14"
                                                {{ isset($filters['next_days']) && $filters['next_days'] == '14' ? 'selected' : '' }}>
                                                14 Days</option>
                                            <option value="21"
                                                {{ isset($filters['next_days']) && $filters['next_days'] == '21' ? 'selected' : '' }}>
                                                21 Days</option>
                                            <option value="28"
                                                {{ isset($filters['next_days']) && $filters['next_days'] == '28' ? 'selected' : '' }}>
                                                28 Days</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="stage" class="form-label">Stage</label>
                                        <select name="stage" id="stage" class="form-select form-select-sm">
                                            <option value="">All Stages</option>
                                            @if (isset($stageInfo) && isset($stageInfo['stages']))
                                                @foreach ($stageInfo['stages'] as $stageOption)
                                                    <option value="{{ $stageOption }}"
                                                        {{ isset($filters['stage']) && $filters['stage'] == $stageOption ? 'selected' : '' }}>
                                                        {{ $stageOption }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="service" class="form-label">Service</label>
                                        <select name="service" id="service" class="form-select form-select-sm">
                                            <option value="">All Services</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}"
                                                    {{ isset($filters['service']) && $filters['service'] == $service->id ? 'selected' : '' }}>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="destination" class="form-label">Destination</label>
                                        <select name="destination" id="destination" class="form-select form-select-sm">
                                            <option value="">All Destinations</option>
                                            @foreach ($destinations as $destination)
                                                <option value="{{ $destination->id }}"
                                                    {{ isset($filters['destination']) && $filters['destination'] == $destination->id ? 'selected' : '' }}>
                                                    {{ $destination->name }}
                                                </option>
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
                                    @can('export reports')
                                        <div class="col-md-4 col-lg-3 align-self-end">
                                            <a href="{{ route('api.accounts.export') }}" class="btn btn-primary btn-sm">
                                                <i data-feather="download" class="me-1"
                                                    style="width: 16px; height: 16px;"></i>
                                                Export
                                            </a>
                                        </div>
                                    @endcan
                                    @if (
                                        !empty($filters['search']) ||
                                            !empty($filters['payment_status']) ||
                                            !empty($filters['travel_date']) ||
                                            !empty($filters['stage']) ||
                                            !empty($filters['next_days']) ||
                                            !empty($filters['service']) ||
                                            !empty($filters['destination']))
                                        <div class="col-md-3 col-lg-2 align-self-end ms-auto">
                                            <a href="{{ route('accounts.index') }}"
                                                class="btn btn-outline-danger w-100 btn-sm">Clear
                                                Filters</a>
                                        </div>
                                    @endif
                                </form>

                                @if (isset($leads) && $leads->count() > 0)
                                    <div class="text-muted small mb-2 px-3">
                                        Showing {{ $leads->firstItem() ?? 0 }} out of {{ $leads->total() }}
                                    </div>
                                @endif

                                <table class="table table-striped small table-bordered w-100 mb-5" id="accountsTable">
                                    <thead>
                                        <tr>
                                            <th>Ref. No.</th>
                                            <th>Customer Name</th>
                                            <th>Destination</th>
                                            <th>Travel Date</th>
                                            <th>Date of Return</th>
                                            <th>Sales Person</th>
                                            <th>Booking Type</th>
                                            <th>Stage</th>
                                            <th>Remark</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leads as $lead)
                                            @php
                                                $firstDestination = $lead->bookingDestinations->first();
                                                $destination = $firstDestination
                                                    ? $firstDestination->destination
                                                    : ($lead->destination
                                                        ? $lead->destination->name
                                                        : '-');
                                                $travelDate =
                                                    $firstDestination && $firstDestination->from_date
                                                        ? $firstDestination->from_date->format('d/m/Y')
                                                        : ($lead->travel_date
                                                            ? $lead->travel_date->format('d/m/Y')
                                                            : '-');
                                                $returnDate =
                                                    $firstDestination && $firstDestination->to_date
                                                        ? $firstDestination->to_date->format('d/m/Y')
                                                        : '-';

                                                $hasHotel = $lead->bookingDestinations->contains(
                                                    fn($d) => $d->hotel_tt || $d->only_hotel,
                                                );
                                                $hasTT = $lead->bookingDestinations->contains(
                                                    fn($d) => $d->hotel_tt || $d->only_tt,
                                                );

                                                $bookingType = '-';
                                                if ($hasHotel && $hasTT) {
                                                    $bookingType = 'HTL + TPT';
                                                } elseif ($hasTT) {
                                                    $bookingType = 'Only TPT';
                                                } elseif ($hasHotel) {
                                                    $bookingType = 'Only HTL';
                                                }
                                            @endphp
                                            <tr data-lead-id="{{ $lead->id }}">
                                                <td><strong>{{ $lead->tsq }}</strong></td>
                                                <td>
                                                    <a href="{{ route('accounts.booking-file', $lead) }}"
                                                        class="text-primary text-decoration-none fw-semibold">
                                                        {{ $lead->customer_name }}
                                                    </a>
                                                </td>
                                                <td>{{ $destination }}</td>
                                                <td>{{ $travelDate }}</td>
                                                <td>{{ $returnDate }}</td>
                                                <td>{{ $lead->assigned_employee?->name ?? ($lead->assignedUser?->name ?? '-') }}
                                                </td>
                                                <td>{{ $bookingType }}</td>
                                                @php
                                                    $stageInfo = \App\Http\Controllers\Controller::getLeadStage($lead);
                                                @endphp
                                                <td>
                                                    <span class="badge {{ $stageInfo['badge_class'] }}">
                                                        {{ $stageInfo['stage'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($lead->latest_booking_file_remark)
                                                        <div class="d-flex align-items-start">
                                                            <div class="flex-grow-1">
                                                                <div class="small text-muted mb-1">
                                                                    <strong>{{ $lead->latest_booking_file_remark->user->name ?? 'Unknown' }}</strong>
                                                                    <span class="ms-2">
                                                                        @if($lead->latest_booking_file_remark->follow_up_at)
                                                                            {{ \Carbon\Carbon::parse($lead->latest_booking_file_remark->follow_up_at)->format('d/m/Y h:i A') }}
                                                                        @else
                                                                            {{ $lead->latest_booking_file_remark->created_at->format('d/m/Y h:i A') }}
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                                <div class="text-truncate" style="max-width: 300px;"
                                                                    title="{{ $lead->latest_booking_file_remark->remark }}">
                                                                    {{ Str::limit($lead->latest_booking_file_remark->remark, 80) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('accounts.booking-file', $lead) }}"
                                                        class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover text-primary"
                                                        data-bs-toggle="tooltip" data-placement="top" title="Booking File">
                                                        <span class="icon">
                                                            <span class="feather-icon">
                                                                <i data-feather="file-text"></i>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">no records found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <!-- Pagination -->
                                @if ($leads->hasPages())
                                    <div class="d-flex justify-content-center mt-4 mb-3 px-3">
                                        {{ $leads->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    <!-- View Lead Modal - Same as bookings page -->
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <div class="col-md-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" id="viewFirstName" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <label class="form-label">Service</label>
                                    <input type="text" id="viewService" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Destination</label>
                                    <input type="text" id="viewDestination" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Travel Date</label>
                                    <input type="text" id="viewTravelDate" class="form-control form-control-sm"
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
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Assignment</h6>
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
                                <span class="badge bg-primary rounded-pill px-3 py-2" id="viewLeadRemarksCount">0</span>
                            </div>
                            <div class="card-body p-4" id="viewLeadRemarks" style="max-height: 400px; overflow-y: auto;">
                                <p class="text-muted text-center mb-0 py-3">No remarks yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100">
                        <form id="leadRemarkForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small mb-1">Add Remark <span
                                            class="text-danger">*</span></label>
                                    <textarea name="remark" class="form-control form-control-sm" rows="2" placeholder="Enter your remark here..."
                                        required></textarea>
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                const leadsBaseUrl = '/leads';

                // Initialize DataTable without search, length menu, ordering, and pagination
                // We use Laravel's server-side pagination instead
                if ($('#accountsTable').length) {
                    $('#accountsTable').DataTable({
                        searching: false, // Disable search box
                        lengthChange: false, // Disable entries per page selector
                        ordering: false, // Disable column ordering
                        info: false, // Disable DataTable info (we use Laravel pagination)
                        paging: false, // Disable DataTable pagination (we use Laravel pagination)
                        dom: 'rt' // Only show table (r), table (t) - no info or pagination
                    });
                }

                // Safe feather replace function
                const safeFeatherReplace = (container) => {
                    if (typeof feather !== 'undefined' && container) {
                        try {
                            feather.replace({}, container);
                        } catch (e) {
                            console.warn('Feather icon replacement failed:', e);
                        }
                    }
                };

                // View Lead Modal Elements
                const viewLeadModalEl = document.getElementById('viewLeadModal');
                const viewLeadLoader = document.getElementById('viewLeadLoader');
                const viewLeadContent = document.getElementById('viewLeadContent');
                const viewLeadAlert = document.getElementById('viewLeadAlert');
                const viewLeadMeta = document.getElementById('viewLeadMeta');
                const viewLeadTitle = document.getElementById('viewLeadModalTitle');
                let viewLeadModalInstance = null;
                let currentLeadId = null;

                // Escape HTML function
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

                // Render remarks function
                const renderRemarks = (remarks) => {
                    if (!remarks || !remarks.length) {
                        return '<p class="text-muted text-center mb-0 py-4"><i data-feather="message-circle" class="me-2" style="width: 16px; height: 16px;"></i>No remarks yet.</p>';
                    }

                    return remarks.map((remark, index) => {
                        const followUp = remark.follow_up_date ?
                            `<span class="badge bg-light text-danger border border-danger ms-2 px-2 py-1">
                            <i data-feather="calendar" class="me-1" style="width: 12px; height: 12px;"></i>
                            Follow-up: ${escapeHtml(remark.follow_up_date)}
                        </span>` : '';
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
                                            ${followUp}
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

                // Reset view lead modal
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
                };

                // Show view lead error
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

                // Load lead details
                const loadLeadDetails = async (leadId) => {
                    if (!leadId || !leadsBaseUrl) {
                        showViewLeadError('Invalid lead.');
                        return Promise.reject('Invalid lead.');
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
                        window.currentLeadData = lead;

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

                        // Populate Customer Information
                        const viewFirstName = document.getElementById('viewFirstName');
                        const viewMiddleName = document.getElementById('viewMiddleName');
                        const viewLastName = document.getElementById('viewLastName');
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
                        const viewLeadService = document.getElementById('viewService');
                        const viewLeadDestination = document.getElementById('viewDestination');
                        const viewLeadTravelDate = document.getElementById('viewTravelDate');
                        if (viewLeadService) {
                            viewLeadService.value = lead.service ?? 'N/A';
                        }
                        if (viewLeadDestination) {
                            viewLeadDestination.value = lead.destination ?? 'N/A';
                        }
                        if (viewLeadTravelDate) {
                            viewLeadTravelDate.value = lead.travel_date ?? 'N/A';
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
                        const viewLeadAssignedUser = document.getElementById('viewAssignedUser');
                        if (viewLeadAssignedUser) {
                            viewLeadAssignedUser.value = lead.assigned_user ?? 'Unassigned';
                        }
                        const viewStatus = document.getElementById('viewStatus');
                        if (viewStatus) {
                            viewStatus.value = lead.status_label ?? lead.status ?? 'N/A';
                        }

                        const viewLeadRemarksCount = document.getElementById('viewLeadRemarksCount');
                        const viewLeadRemarksContainer = document.getElementById('viewLeadRemarks');
                        if (viewLeadRemarksCount) {
                            viewLeadRemarksCount.textContent = data.remarks?.length ?? 0;
                        }
                        if (viewLeadRemarksContainer) {
                            viewLeadRemarksContainer.innerHTML = renderRemarks(data.remarks || []);
                        }

                        // Initialize Feather icons after content is loaded
                        safeFeatherReplace(viewLeadContent);

                        return Promise.resolve(data);
                    } catch (error) {
                        console.error(error);
                        showViewLeadError(error.message || 'Unexpected error occurred.');
                        return Promise.reject(error);
                    }
                };

                // Store loadLeadDetails on window for access in global event handler
                window.loadLeadDetails = loadLeadDetails;

                // Remark form submission
                const remarkForm = document.getElementById('leadRemarkForm');
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

                            // Show success message
                            if (viewLeadAlert) {
                                viewLeadAlert.classList.remove('d-none');
                                viewLeadAlert.classList.remove('alert-danger');
                                viewLeadAlert.classList.add('alert-success');
                                viewLeadAlert.textContent = payload?.message ||
                                    'Remark added successfully!';
                            }

                            remarkForm.reset();

                            // Reload remarks
                            if (typeof window.loadLeadDetails === 'function') {
                                window.loadLeadDetails(currentLeadId);
                            }
                        } catch (error) {
                            if (viewLeadAlert) {
                                viewLeadAlert.classList.remove('d-none');
                                viewLeadAlert.classList.remove('alert-success');
                                viewLeadAlert.classList.add('alert-danger');
                                viewLeadAlert.textContent = error.message || 'Unable to add remark.';
                            }
                        }
                    });
                }

                // Initialize modal instance
                if (viewLeadModalEl && typeof bootstrap !== 'undefined') {
                    if (!viewLeadModalInstance) {
                        viewLeadModalInstance = new bootstrap.Modal(viewLeadModalEl, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        window.viewLeadModalInstance = viewLeadModalInstance;
                    }
                }

                // View Lead click handler removed - Accounts department only sees Booking File

                if (viewLeadModalEl) {
                    viewLeadModalEl.addEventListener('shown.bs.modal', () => {
                        safeFeatherReplace(viewLeadModalEl);
                    });

                    viewLeadModalEl.addEventListener('hidden.bs.modal', () => {
                        currentLeadId = null;
                        resetViewLeadModal();
                    });
                }

                // Initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                // Initialize Bootstrap tooltips
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        try {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        } catch (e) {
                            console.warn('Tooltip initialization failed:', e);
                            return null;
                        }
                    });
                }

            });
        </script>
    @endpush
@endsection
