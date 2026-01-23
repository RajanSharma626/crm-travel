@extends('layouts.app')
@section('title', 'Delivery | Travel Shravel')
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

                                <form method="GET" action="{{ route('deliveries.index') }}" class="row g-3 mb-4"
                                    id="deliveriesFiltersForm">
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
                                    <div class="col-md-4 col-lg-3">
                                        <label for="search" class="form-label">Search</label>
                                        <input type="text" name="search" id="search"
                                            class="form-control form-control-sm" placeholder="Enter name, ref no., or phone"
                                            value="{{ $filters['search'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="delivery_status" class="form-label">Delivery Status</label>
                                        <div class="d-flex">
                                            <select name="delivery_status" id="delivery_status"
                                                class="form-select form-select-sm">
                                                <option value="">All Status</option>
                                                <option value="Pending"
                                                    {{ ($filters['delivery_status'] ?? '') == 'Pending' ? 'selected' : '' }}>
                                                    Pending
                                                </option>
                                                <option value="In_Process"
                                                    {{ ($filters['delivery_status'] ?? '') == 'In_Process' ? 'selected' : '' }}>
                                                    In
                                                    Process</option>
                                                <option value="Delivered"
                                                    {{ ($filters['delivery_status'] ?? '') == 'Delivered' ? 'selected' : '' }}>
                                                    Delivered</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm ms-2 d-flex"> <i
                                                    class="ri-search-line me-1"></i> Filter</button>
                                        </div>
                                    </div>
                                    @if (
                                        !empty($filters['search']) ||
                                            !empty($filters['delivery_status']) ||
                                            !empty($filters['travel_date']) ||
                                            !empty($filters['stage']) ||
                                            !empty($filters['next_days']))
                                        <div class="col-md-3 col-lg-2 align-self-end ms-auto">
                                            <a href="{{ route('deliveries.index') }}"
                                                class="btn btn-outline-danger w-100 btn-sm">Clear
                                                Filters</a>
                                        </div>
                                    @endif
                                </form>

                                <!-- Delivery Table -->
                                @if (isset($leads) && $leads->count() > 0)
                                    <div class="text-muted small mb-2 px-3">
                                        Showing {{ $leads->firstItem() ?? 0 }} out of {{ $leads->total() }}
                                    </div>
                                @endif

                                <table class="table table-striped small table-bordered w-100 mb-5" id="deliveriesTable">
                                    <thead>
                                        <tr>
                                            <th>Ref No.</th>
                                            <th>Customer Name</th>
                                            <th>Phone</th>
                                            <th>Delivery Status</th>
                                            <th>Assigned To</th>
                                            <th>Delivery Method</th>
                                            <th>Courier ID</th>
                                            <th>Delivered At</th>
                                            <th>Booking File Recent Remark</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leads as $lead)
                                            @php
                                                $delivery = $lead->delivery;
                                                $deliveryStatus = $delivery ? $delivery->delivery_status : 'Pending';
                                            @endphp
                                            <tr data-lead-id="{{ $lead->id }}">
                                                <td><strong>{{ $lead->tsq }}</strong></td>
                                                <td>
                                                    <a href="{{ route('deliveries.booking-file', $lead) }}"
                                                        class="text-primary text-decoration-none fw-semibold">
                                                        {{ $lead->customer_name }}
                                                    </a>
                                                </td>
                                                <td>{{ $lead->primary_phone ?? $lead->phone }}</td>

                                                @php
                                                    $stageInfo = \App\Http\Controllers\Controller::getLeadStage($lead);
                                                @endphp
                                                <td>
                                                    <span class="badge {{ $stageInfo['badge_class'] }}">
                                                        {{ $stageInfo['stage'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $lead->assignedUser ? $lead->assignedUser->name : 'Unassigned' }}
                                                </td>
                                                <td>
                                                    @if ($delivery && $delivery->delivery_method)
                                                        {{ ucfirst(str_replace('_', ' ', $delivery->delivery_method)) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $delivery && $delivery->courier_id ? $delivery->courier_id : '-' }}
                                                </td>
                                                <td>
                                                    @if ($delivery && $delivery->delivered_at)
                                                        {{ $delivery->delivered_at->format('d M, Y h:i A') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($lead->latest_booking_file_remark)
                                                        <div class="d-flex align-items-start">
                                                            <div class="flex-grow-1">
                                                                <div class="small text-muted mb-1">
                                                                    <strong>{{ $lead->latest_booking_file_remark->user->name ?? 'Unknown' }}</strong>
                                                                    <span
                                                                        class="ms-2">{{ $lead->latest_booking_file_remark->created_at->format('d/m/Y h:i A') }}</span>
                                                                </div>
                                                                <div class="text-truncate" style="max-width: 200px;"
                                                                    title="{{ $lead->latest_booking_file_remark->remark }}">
                                                                    {{ Str::limit($lead->latest_booking_file_remark->remark, 50) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex">
                                                            <a href="{{ route('deliveries.booking-file', $lead) }}"
                                                                class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover text-primary"
                                                                data-bs-toggle="tooltip" data-placement="top"
                                                                title="Booking File">
                                                                <span class="icon">
                                                                    <span class="feather-icon">
                                                                        <i data-feather="file-text"></i>
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center">No deliveries found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <!-- Pagination -->
                                @if ($leads->hasPages())
                                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3 px-3">
                                        <div class="text-muted small">
                                            Showing {{ $leads->firstItem() ?? 0 }} to {{ $leads->lastItem() ?? 0 }} of
                                            {{ $leads->total() }} entries
                                        </div>
                                        <div>
                                            {{ $leads->links('pagination::bootstrap-5') }}
                                        </div>
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
                                    <div class="col-md-4">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="editFirstName"
                                            placeholder="e.g. Ramesh" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-4">
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
                                    <div class="col-md-4">
                                        <label class="form-label">Service <span class="text-danger">*</span></label>
                                        <select name="service_id" id="editServiceId" class="form-select form-select-sm"
                                            required>
                                            <option value="">-- Select Service --</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Destination <span class="text-danger">*</span></label>
                                        <select name="destination_id" id="editDestinationId"
                                            class="form-select form-select-sm" required>
                                            <option value="">-- Select Destination --</option>
                                            @foreach ($destinations as $destination)
                                                <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Travel Date</label>
                                        <input type="date" name="travel_date" id="editTravelDate"
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
                                        <select name="assigned_user_id" id="editAssignedUserId"
                                            class="form-select form-select-sm">
                                            <option value="">-- Select User --</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                    ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="editStatus" class="form-select form-select-sm"
                                            required>
                                            <option value="new">New</option>
                                            <option value="contacted">Contacted</option>
                                            <option value="follow_up">Follow Up</option>
                                            <option value="priority">Priority</option>
                                            <option value="booked" selected>Booked</option>
                                            <option value="closed">Closed</option>
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

    <!-- Assign Delivery Modal -->
    <div class="modal fade" id="assignDeliveryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="assignDeliveryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="assignDeliveryModalLabel">
                        <i data-feather="user-plus" class="me-2" style="width: 20px; height: 20px;"></i>
                        Assign Delivery
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="assignDeliveryAlert" class="alert d-none mb-3" role="alert"></div>
                    <form id="assignDeliveryForm">
                        @csrf
                        <input type="hidden" id="assignDeliveryLeadId" name="lead_id">
                        <input type="hidden" id="assignDeliveryId" name="delivery_id">

                        <div class="mb-3">
                            <label class="form-label">Assign To <span class="text-danger">*</span></label>
                            <select name="assigned_to_delivery_user_id" id="assignDeliveryUserId" class="form-select"
                                required>
                                <option value="">-- Select Delivery User --</option>
                                @if (isset($deliveryUsers) && is_iterable($deliveryUsers))
                                    @foreach ($deliveryUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Delivery Method</label>
                            <select name="delivery_method" id="assignDeliveryMethod" class="form-select">
                                <option value="">-- Select Method --</option>
                                <option value="soft_copy">Soft Copy</option>
                                <option value="courier">Courier</option>
                                <option value="hand_delivery">Hand Delivery</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" id="assignDeliveryRemarks" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Assign Delivery</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Delivery Status Modal -->
    <div class="modal fade" id="updateDeliveryStatusModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="updateDeliveryStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="updateDeliveryStatusModalLabel">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i>
                        Update Delivery Status
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="updateStatusAlert" class="alert d-none mb-3" role="alert"></div>
                    <form id="updateDeliveryStatusForm">
                        @csrf
                        <input type="hidden" id="updateStatusDeliveryId" name="delivery_id">

                        <div class="mb-3">
                            <label class="form-label">Delivery Status <span class="text-danger">*</span></label>
                            <select name="delivery_status" id="updateStatusDeliveryStatus" class="form-select" required>
                                <option value="Pending">Pending</option>
                                <option value="In_Process">In Process</option>
                                <option value="Delivered">Delivered</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Courier ID</label>
                            <input type="text" name="courier_id" id="updateStatusCourierId" class="form-control"
                                placeholder="Enter courier tracking ID">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Delivery Method</label>
                            <select name="delivery_method" id="updateStatusDeliveryMethod" class="form-select">
                                <option value="">-- Select Method --</option>
                                <option value="soft_copy">Soft Copy</option>
                                <option value="courier">Courier</option>
                                <option value="hand_delivery">Hand Delivery</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" id="updateStatusRemarks" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
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
                if ($('#deliveriesTable').length) {
                    $('#deliveriesTable').DataTable({
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

                // Initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
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

                // Edit Lead Functionality
                const editLeadContent = document.getElementById('editLeadContent');
                const editLeadForm = document.getElementById('editLeadForm');
                const editLeadAlert = document.getElementById('editLeadAlert');
                const cancelEditFormBtn = document.getElementById('cancelEditFormBtn');
                let currentEditLeadId = null;

                // Function to update children total for edit form
                const updateEditChildrenTotal = () => {
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
                    document.getElementById('editAdults').value = lead.adults || 0;
                    document.getElementById('editChildren25').value = lead.children_2_5 || 0;
                    document.getElementById('editChildren611').value = lead.children_6_11 || 0;
                    document.getElementById('editInfants').value = lead.infants || 0;
                    document.getElementById('editAssignedUserId').value = lead.assigned_user_id || '';
                    document.getElementById('editStatus').value = lead.status || 'booked';

                    // Update children total
                    updateEditChildrenTotal();
                };

                // Function to switch to edit mode
                const switchToEditMode = () => {
                    if (viewLeadContent) viewLeadContent.classList.add('d-none');
                    if (editLeadContent) editLeadContent.classList.remove('d-none');
                    if (viewLeadTitle) viewLeadTitle.textContent = 'Edit Lead';
                    if (typeof feather !== 'undefined') feather.replace();
                };

                // Function to switch back to view mode
                const switchToViewMode = () => {
                    if (viewLeadContent) viewLeadContent.classList.remove('d-none');
                    if (editLeadContent) editLeadContent.classList.add('d-none');
                    if (viewLeadTitle) viewLeadTitle.textContent = 'Lead Details';
                    if (editLeadAlert) {
                        editLeadAlert.classList.add('d-none');
                        editLeadAlert.textContent = '';
                    }
                };

                // Cancel edit button handler
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
                                // Reload page to refresh table
                                window.location.reload();
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

                // Edit button click handler - opens modal in edit mode
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

                        currentEditLeadId = leadId;
                        currentLeadId = leadId;

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

                            // Reset modal state
                            resetViewLeadModal();
                            if (viewLeadLoader) {
                                viewLeadLoader.classList.remove('d-none');
                            }

                            // Load lead data for editing
                            fetch(`${leadsBaseUrl}/${leadId}?modal=1`, {
                                    headers: {
                                        'Accept': 'application/json',
                                    },
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.lead) {
                                        window.currentLeadData = data.lead;

                                        // Hide loader
                                        if (viewLeadLoader) {
                                            viewLeadLoader.classList.add('d-none');
                                        }

                                        // Populate edit form with current lead data
                                        populateEditForm(data.lead);

                                        // Switch to edit mode
                                        switchToEditMode();

                                        // Initialize Feather icons
                                        safeFeatherReplace(viewLeadModalEl);
                                    } else {
                                        throw new Error('Lead details not found.');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading lead for edit:', error);
                                    if (viewLeadLoader) {
                                        viewLeadLoader.classList.add('d-none');
                                    }
                                    if (editLeadAlert) {
                                        editLeadAlert.classList.remove('d-none');
                                        editLeadAlert.classList.add('alert-danger');
                                        editLeadAlert.textContent = 'Unable to load lead details for editing.';
                                    }
                                });
                        } else {
                            console.error('Bootstrap modal not available');
                        }
                    };

                    document.addEventListener('click', window.editLeadClickHandler, true);
                }

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

                // View Lead click handler
                if (!window.viewLeadClickHandler) {
                    window.viewLeadClickHandler = function(event) {
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

                        const modalEl = document.getElementById('viewLeadModal');
                        if (!modalEl) {
                            console.error('View lead modal element not found');
                            return;
                        }

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
                            if (typeof window.loadLeadDetails === 'function') {
                                window.loadLeadDetails(leadId);
                            } else {
                                console.error('loadLeadDetails function not found on window');
                            }
                        } else {
                            console.error('Bootstrap modal not available');
                        }
                    };

                    document.addEventListener('click', window.viewLeadClickHandler, true);
                }

                if (viewLeadModalEl) {
                    viewLeadModalEl.addEventListener('shown.bs.modal', () => {
                        safeFeatherReplace(viewLeadModalEl);
                    });

                    viewLeadModalEl.addEventListener('hidden.bs.modal', () => {
                        currentLeadId = null;
                        currentEditLeadId = null;
                        resetViewLeadModal();
                        switchToViewMode();
                    });
                }

                // Assign Delivery Modal
                const assignDeliveryModalEl = document.getElementById('assignDeliveryModal');
                const assignDeliveryModal = assignDeliveryModalEl ? new bootstrap.Modal(assignDeliveryModalEl) : null;
                const assignDeliveryForm = document.getElementById('assignDeliveryForm');

                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('.assign-delivery-btn');
                    if (btn) {
                        e.preventDefault();
                        const leadId = btn.dataset.leadId;
                        const deliveryId = btn.dataset.deliveryId;

                        document.getElementById('assignDeliveryLeadId').value = leadId;
                        document.getElementById('assignDeliveryId').value = deliveryId || '';
                        if (assignDeliveryModal) assignDeliveryModal.show();
                    }
                });

                if (assignDeliveryForm) {
                    assignDeliveryForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const formData = new FormData(assignDeliveryForm);
                        const alertEl = document.getElementById('assignDeliveryAlert');
                        const deliveryId = formData.get('delivery_id');
                        const leadId = formData.get('lead_id');

                        try {
                            let url, method;
                            if (deliveryId) {
                                // Update existing delivery
                                url = `/api/delivery/${deliveryId}/assign`;
                                method = 'POST';
                            } else {
                                // Create new delivery
                                url = `/leads/${leadId}/deliveries`;
                                method = 'POST';
                            }

                            const response = await fetch(url, {
                                method: method,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')?.content
                                },
                                body: formData
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                throw new Error(data.message || 'Failed to assign delivery');
                            }

                            if (alertEl) {
                                alertEl.classList.remove('d-none', 'alert-danger');
                                alertEl.classList.add('alert-success');
                                alertEl.textContent = data.message || 'Delivery assigned successfully!';
                            }

                            setTimeout(() => {
                                if (assignDeliveryModal) assignDeliveryModal.hide();
                                location.reload();
                            }, 1500);
                        } catch (error) {
                            if (alertEl) {
                                alertEl.classList.remove('d-none', 'alert-success');
                                alertEl.classList.add('alert-danger');
                                alertEl.textContent = error.message || 'Failed to assign delivery';
                            }
                        }
                    });
                }

                // Update Delivery Status Modal
                const updateStatusModalEl = document.getElementById('updateDeliveryStatusModal');
                const updateStatusModal = updateStatusModalEl ? new bootstrap.Modal(updateStatusModalEl) : null;
                const updateStatusForm = document.getElementById('updateDeliveryStatusForm');

                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('.update-delivery-status-btn');
                    if (btn) {
                        e.preventDefault();
                        const deliveryId = btn.dataset.deliveryId;
                        const leadId = btn.dataset.leadId;
                        const row = btn.closest('tr');

                        if (deliveryId || leadId) {
                            document.getElementById('updateStatusDeliveryId').value = deliveryId || leadId;
                            // Get current status from the row's status badge
                            const statusBadge = row ? row.querySelector('.badge') : null;
                            let currentStatus = 'Pending';
                            if (statusBadge) {
                                if (statusBadge.textContent.trim() === 'Pending') {
                                    currentStatus = 'Pending';
                                } else if (statusBadge.textContent.trim() === 'In Process') {
                                    currentStatus = 'In_Process';
                                } else if (statusBadge.textContent.trim() === 'Delivered') {
                                    currentStatus = 'Delivered';
                                }
                            }
                            document.getElementById('updateStatusDeliveryStatus').value = currentStatus;
                            if (updateStatusModal) updateStatusModal.show();
                        }
                    }
                });

                if (updateStatusForm) {
                    updateStatusForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const formData = new FormData(updateStatusForm);
                        const alertEl = document.getElementById('updateStatusAlert');
                        const deliveryId = formData.get('delivery_id');

                        try {
                            const response = await fetch(`/api/delivery/${deliveryId}/status`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')?.content
                                },
                                body: JSON.stringify({
                                    delivery_status: formData.get('delivery_status'),
                                    courier_id: formData.get('courier_id'),
                                    delivery_method: formData.get('delivery_method'),
                                    remarks: formData.get('remarks')
                                })
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                throw new Error(data.message || 'Failed to update delivery status');
                            }

                            if (alertEl) {
                                alertEl.classList.remove('d-none', 'alert-danger');
                                alertEl.classList.add('alert-success');
                                alertEl.textContent = data.message ||
                                    'Delivery status updated successfully!';
                            }

                            setTimeout(() => {
                                if (updateStatusModal) updateStatusModal.hide();
                                location.reload();
                            }, 1500);
                        } catch (error) {
                            if (alertEl) {
                                alertEl.classList.remove('d-none', 'alert-success');
                                alertEl.classList.add('alert-danger');
                                alertEl.textContent = error.message || 'Failed to update delivery status';
                            }
                        }
                    });
                }

                // Initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });
        </script>
    @endpush
@endsection
