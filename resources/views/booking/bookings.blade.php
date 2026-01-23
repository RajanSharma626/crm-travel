@extends('layouts.app')
@section('title', 'Bookings | Travel Shravel')
@section('content')
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

                                <form method="GET" action="{{ route('bookings.index') }}" class="row g-3 mb-4"
                                    id="bookingFiltersForm">
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
                                        <div class="d-flex">
                                            <input type="text" name="search" id="search"
                                                class="form-control form-control-sm"
                                                placeholder="Enter name, ref no., or phone"
                                                value="{{ $filters['search'] ?? '' }}">
                                            <button type="submit" class="btn btn-primary btn-sm ms-2 d-flex"> <i
                                                    class="ri-search-line me-1"></i> Filter</button>
                                        </div>
                                    </div>
                                    @if ($filters['search'] || !empty($filters['travel_date']) || !empty($filters['stage']) || !empty($filters['next_days']))
                                        <div class="col-md-3 col-lg-2 align-self-end ms-auto">
                                            <a href="{{ route('bookings.index') }}"
                                                class="btn btn-outline-danger w-100 btn-sm">Clear
                                                Filters</a>
                                        </div>
                                    @endif
                                </form>

                                <table class="table table-striped small table-bordered w-100 mb-5" id="bookingsTable">
                                    <thead>
                                        <tr>
                                            <th>Ref No.</th>
                                            <th>Customer Name</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Remark</th>
                                            <th>Created On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leads as $lead)
                                            @php
                                                $stageInfo = \App\Http\Controllers\Controller::getLeadStage($lead);
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $lead->tsq }}</strong></td>
                                                <td>
                                                    <a href="{{ route('bookings.form', $lead) }}"
                                                        class="text-primary text-decoration-none fw-semibold">
                                                        {{ $lead->customer_name }}
                                                    </a>
                                                </td>
                                                <td>{{ $lead->primary_phone ?? $lead->phone }}</td>

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
                                                <td>{{ $lead->created_at->format('d M, Y') }}</td>
                                                <td>
                                                    @php
                                                        $user = Auth::user();
                                                        $role = $user->role ?? $user->getRoleNameAttribute();
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
                                                            <a href="{{ route('bookings.form', $lead) }}"
                                                                class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover text-primary"
                                                                data-bs-toggle="tooltip" data-placement="top"
                                                                title="Booking File"> <span class="icon"> <span
                                                                        class="feather-icon"> <i
                                                                            data-feather="file-text"></i> </span>
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No bookings found</td>
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

    <!-- Booking File Modal -->
    <div class="modal fade" id="bookingFileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="bookingFileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="bookingFileLoader" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                        <p class="mt-3 text-muted mb-0">Loading booking file...</p>
                    </div>

                    <form id="bookingFileForm" class="d-none">
                        @csrf
                        <input type="hidden" name="lead_id" id="bookingFileLeadId">

                        <!-- Booking Reference Section -->
                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                <i data-feather="tag" class="me-1" style="width: 14px; height: 14px;"></i>
                                Booking Reference
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">TSQ Number</label>
                                    <input type="text" name="tsq" id="bookingTsq"
                                        class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Booking Reference</label>
                                    <input type="text" name="booking_reference" id="bookingReference"
                                        class="form-control form-control-sm" placeholder="Enter booking reference">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Booking Date</label>
                                    <input type="date" name="booking_date" id="bookingDate"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Travel Agent</label>
                                    <input type="text" name="travel_agent" id="bookingTravelAgent"
                                        class="form-control form-control-sm" placeholder="Agent name">
                                </div>
                            </div>
                        </div>

                        <!-- Customer Details Section -->
                        <div class="mb-4 border rounded-3 p-3">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                <i data-feather="user" class="me-1" style="width: 14px; height: 14px;"></i>
                                Customer Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" id="bookingCustomerName" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" id="bookingEmail" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phone</label>
                                    <input type="text" id="bookingPhone" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <textarea id="bookingAddress" class="form-control form-control-sm" rows="2" readonly></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Travel Details Section -->
                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                <i data-feather="map-pin" class="me-1" style="width: 14px; height: 14px;"></i>
                                Travel Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Service</label>
                                    <input type="text" id="bookingService" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Destination</label>
                                    <input type="text" id="bookingDestination" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Travel Date</label>
                                    <input type="date" name="travel_date" id="bookingTravelDate"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Return Date</label>
                                    <input type="date" name="return_date" id="bookingReturnDate"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Duration (Days)</label>
                                    <input type="number" name="duration" id="bookingDuration"
                                        class="form-control form-control-sm" min="1" placeholder="Nights">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Adults</label>
                                    <input type="number" id="bookingAdults" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Children</label>
                                    <input type="number" id="bookingChildren" class="form-control form-control-sm"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Infants</label>
                                    <input type="number" id="bookingInfants" class="form-control form-control-sm"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Hotel Details Section -->
                        <div class="mb-4 border rounded-3 p-3">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                <i data-feather="home" class="me-1" style="width: 14px; height: 14px;"></i>
                                Hotel Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Hotel Name</label>
                                    <input type="text" name="hotel_name" id="bookingHotelName"
                                        class="form-control form-control-sm" placeholder="Hotel name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hotel Category</label>
                                    <select name="hotel_category" id="bookingHotelCategory"
                                        class="form-select form-select-sm">
                                        <option value="">-- Select --</option>
                                        <option value="3 Star">3 Star</option>
                                        <option value="4 Star">4 Star</option>
                                        <option value="5 Star">5 Star</option>
                                        <option value="Luxury">Luxury</option>
                                        <option value="Budget">Budget</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Room Type</label>
                                    <input type="text" name="room_type" id="bookingRoomType"
                                        class="form-control form-control-sm" placeholder="e.g. Deluxe, Suite">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Check-in Date</label>
                                    <input type="date" name="checkin_date" id="bookingCheckinDate"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Check-out Date</label>
                                    <input type="date" name="checkout_date" id="bookingCheckoutDate"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Meal Plan</label>
                                    <select name="meal_plan" id="bookingMealPlan" class="form-select form-select-sm">
                                        <option value="">-- Select --</option>
                                        <option value="Room Only">Room Only</option>
                                        <option value="Breakfast">Breakfast</option>
                                        <option value="Half Board">Half Board</option>
                                        <option value="Full Board">Full Board</option>
                                        <option value="All Inclusive">All Inclusive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Details Section -->
                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                <i data-feather="navigation" class="me-1" style="width: 14px; height: 14px;"></i>
                                Flight Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Airline</label>
                                    <input type="text" name="airline" id="bookingAirline"
                                        class="form-control form-control-sm" placeholder="Airline name">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Flight Number</label>
                                    <input type="text" name="flight_number" id="bookingFlightNumber"
                                        class="form-control form-control-sm" placeholder="e.g. AI 101">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Departure Date</label>
                                    <input type="date" name="departure_date" id="bookingDepartureDate"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Departure Time</label>
                                    <input type="time" name="departure_time" id="bookingDepartureTime"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Return Flight Number</label>
                                    <input type="text" name="return_flight_number" id="bookingReturnFlightNumber"
                                        class="form-control form-control-sm" placeholder="e.g. AI 102">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Return Date</label>
                                    <input type="date" name="return_flight_date" id="bookingReturnFlightDate"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Return Time</label>
                                    <input type="time" name="return_flight_time" id="bookingReturnFlightTime"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Class</label>
                                    <select name="flight_class" id="bookingFlightClass"
                                        class="form-select form-select-sm">
                                        <option value="">-- Select --</option>
                                        <option value="Economy">Economy</option>
                                        <option value="Premium Economy">Premium Economy</option>
                                        <option value="Business">Business</option>
                                        <option value="First Class">First Class</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Package Details Section -->
                        <div class="mb-4 border rounded-3 p-3">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                <i data-feather="package" class="me-1" style="width: 14px; height: 14px;"></i>
                                Package Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Package Type</label>
                                    <select name="package_type" id="bookingPackageType"
                                        class="form-select form-select-sm">
                                        <option value="">-- Select --</option>
                                        <option value="Standard">Standard</option>
                                        <option value="Deluxe">Deluxe</option>
                                        <option value="Premium">Premium</option>
                                        <option value="Custom">Custom</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Inclusions</label>
                                    <textarea name="inclusions" id="bookingInclusions" class="form-control form-control-sm" rows="3"
                                        placeholder="List package inclusions"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Exclusions</label>
                                    <textarea name="exclusions" id="bookingExclusions" class="form-control form-control-sm" rows="3"
                                        placeholder="List package exclusions"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Special Requests</label>
                                    <textarea name="special_requests" id="bookingSpecialRequests" class="form-control form-control-sm" rows="3"
                                        placeholder="Any special requests or notes"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Section -->
                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                <i data-feather="dollar-sign" class="me-1" style="width: 14px; height: 14px;"></i>
                                Pricing
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Selling Price</label>
                                    <input type="number" name="selling_price" id="bookingSellingPrice"
                                        class="form-control form-control-sm" step="0.01" min="0"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Booked Value</label>
                                    <input type="number" name="booked_value" id="bookingBookedValue"
                                        class="form-control form-control-sm" step="0.01" min="0"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Total Cost</label>
                                    <input type="number" name="total_cost" id="bookingTotalCost"
                                        class="form-control form-control-sm" step="0.01" min="0"
                                        placeholder="0.00" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Profit</label>
                                    <input type="number" id="bookingProfit" class="form-control form-control-sm"
                                        step="0.01" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Schedule Section -->
                        <div class="mb-4 border rounded-3 p-3">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                <i data-feather="credit-card" class="me-1" style="width: 14px; height: 14px;"></i>
                                Payment Schedule
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Advance Amount</label>
                                    <input type="number" name="advance_amount" id="bookingAdvanceAmount"
                                        class="form-control form-control-sm" step="0.01" min="0"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Advance Date</label>
                                    <input type="date" name="advance_date" id="bookingAdvanceDate"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Balance Amount</label>
                                    <input type="number" name="balance_amount" id="bookingBalanceAmount"
                                        class="form-control form-control-sm" step="0.01" min="0"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Balance Due Date</label>
                                    <input type="date" name="balance_due_date" id="bookingBalanceDueDate"
                                        class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Documents Checklist Section -->
                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                <i data-feather="file-text" class="me-1" style="width: 14px; height: 14px;"></i>
                                Documents Checklist
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_passport"
                                            id="bookingDocPassport">
                                        <label class="form-check-label" for="bookingDocPassport">Passport</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_visa"
                                            id="bookingDocVisa">
                                        <label class="form-check-label" for="bookingDocVisa">Visa</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_ticket"
                                            id="bookingDocTicket">
                                        <label class="form-check-label" for="bookingDocTicket">Ticket</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_voucher"
                                            id="bookingDocVoucher">
                                        <label class="form-check-label" for="bookingDocVoucher">Voucher</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_invoice"
                                            id="bookingDocInvoice">
                                        <label class="form-check-label" for="bookingDocInvoice">Invoice</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_insurance"
                                            id="bookingDocInsurance">
                                        <label class="form-check-label" for="bookingDocInsurance">Insurance</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_other"
                                            id="bookingDocOther">
                                        <label class="form-check-label" for="bookingDocOther">Other</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Booking File</button>
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

                // DataTable removed - using standard HTML table with Laravel pagination

                // Safe feather replace function
                const safeFeatherReplace = (container) => {
                    if (typeof feather === 'undefined' || !feather.icons) return;

                    // Use setTimeout to ensure DOM is ready
                    setTimeout(function() {
                        try {
                            // Validate icons exist before replacing
                            const selector = container ? container.querySelectorAll('[data-feather]') :
                                document.querySelectorAll('[data-feather]');

                            if (selector && selector.length > 0) {
                                // Replace icons, validating each one individually
                                const icons = Array.from(selector);
                                icons.forEach(function(icon) {
                                    try {
                                        // Skip if already replaced (has SVG content)
                                        if (icon.tagName === 'svg' || icon.querySelector('svg')) {
                                            return;
                                        }

                                        const iconName = icon.getAttribute('data-feather');
                                        if (!iconName) return;

                                        // Validate icon exists and has toSvg method
                                        const iconObj = feather.icons[iconName];
                                        if (!iconObj || typeof iconObj.toSvg !== 'function') {
                                            // Icon doesn't exist, skip silently
                                            return;
                                        }

                                        // Get icon attributes
                                        const attrs = {};
                                        Array.from(icon.attributes).forEach(function(attr) {
                                            if (attr.name !== 'data-feather' && attr
                                                .name !== 'class') {
                                                attrs[attr.name] = attr.value;
                                            }
                                        });

                                        // Replace icon
                                        const svg = iconObj.toSvg(attrs);
                                        if (svg) {
                                            icon.outerHTML = svg;
                                        }
                                    } catch (e) {
                                        // Skip invalid icons silently - don't log to avoid console spam
                                    }
                                });
                            }
                        } catch (e) {
                            console.warn('Feather icon replacement error:', e);
                        }
                    }, 10);
                };


                // Initialize feather icons
                safeFeatherReplace();

                // Booking File Modal
                const bookingFileModalEl = document.getElementById('bookingFileModal');
                const bookingFileForm = document.getElementById('bookingFileForm');
                const bookingFileLoader = document.getElementById('bookingFileLoader');

                // Handle booking file button clicks
                document.addEventListener('click', function(event) {
                    if (event.target.closest('.booking-file-btn')) {
                        event.preventDefault();
                        const btn = event.target.closest('.booking-file-btn');
                        const leadId = btn.getAttribute('data-lead-id');

                        if (!leadId) return;

                        // Show loader, hide form
                        if (bookingFileLoader) bookingFileLoader.classList.remove('d-none');
                        if (bookingFileForm) bookingFileForm.classList.add('d-none');

                        // Open modal
                        if (bookingFileModalEl && typeof bootstrap !== 'undefined') {
                            const modalInstance = bootstrap.Modal.getOrCreateInstance(bookingFileModalEl);
                            modalInstance.show();
                        }

                        // Fetch lead data
                        fetch(`${leadsBaseUrl}/${leadId}?modal=1`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.lead) {
                                    populateBookingFileForm(data.lead);
                                }
                            })
                            .catch(error => {
                                console.error('Error loading booking file:', error);
                                alert('Unable to load booking file data.');
                            })
                            .finally(() => {
                                // Hide loader, show form
                                if (bookingFileLoader) bookingFileLoader.classList.add('d-none');
                                if (bookingFileForm) bookingFileForm.classList.remove('d-none');

                                // Initialize Feather icons
                                safeFeatherReplace(bookingFileModalEl);
                            });
                    }
                });

                // Function to populate booking file form
                function populateBookingFileForm(lead) {
                    // Set lead ID
                    if (document.getElementById('bookingFileLeadId')) {
                        document.getElementById('bookingFileLeadId').value = lead.id;
                    }

                    // Booking Reference
                    if (document.getElementById('bookingTsq')) {
                        document.getElementById('bookingTsq').value = lead.tsq || '';
                    }
                    if (document.getElementById('bookingDate')) {
                        document.getElementById('bookingDate').value = lead.created_at ? new Date(lead.created_at)
                            .toISOString().split('T')[0] : '';
                    }

                    // Customer Details (readonly)
                    if (document.getElementById('bookingCustomerName')) {
                        document.getElementById('bookingCustomerName').value = lead.customer_name || '';
                    }
                    if (document.getElementById('bookingEmail')) {
                        document.getElementById('bookingEmail').value = lead.email || '';
                    }
                    if (document.getElementById('bookingPhone')) {
                        document.getElementById('bookingPhone').value = lead.primary_phone || lead.phone || '';
                    }
                    if (document.getElementById('bookingAddress')) {
                        const addressParts = [
                            lead.address_line,
                            lead.city,
                            lead.state,
                            lead.country,
                            lead.pin_code
                        ].filter(Boolean);
                        document.getElementById('bookingAddress').value = addressParts.join(', ') || '';
                    }

                    // Travel Details
                    if (document.getElementById('bookingService')) {
                        document.getElementById('bookingService').value = lead.service?.name || '';
                    }
                    if (document.getElementById('bookingDestination')) {
                        document.getElementById('bookingDestination').value = lead.destination?.name || '';
                    }
                    if (document.getElementById('bookingTravelDate')) {
                        document.getElementById('bookingTravelDate').value = lead.travel_date_raw || '';
                    }
                    if (document.getElementById('bookingAdults')) {
                        document.getElementById('bookingAdults').value = lead.adults || 0;
                    }
                    if (document.getElementById('bookingChildren')) {
                        const totalChildren = (parseInt(lead.children_2_5 || 0) + parseInt(lead.children_6_11 || 0));
                        document.getElementById('bookingChildren').value = totalChildren || 0;
                    }
                    if (document.getElementById('bookingInfants')) {
                        document.getElementById('bookingInfants').value = lead.infants || 0;
                    }

                    // Pricing
                    if (document.getElementById('bookingSellingPrice')) {
                        document.getElementById('bookingSellingPrice').value = lead.selling_price || '';
                    }
                    if (document.getElementById('bookingBookedValue')) {
                        document.getElementById('bookingBookedValue').value = lead.booked_value || '';
                    }

                    // Calculate profit
                    updateBookingProfit();
                }

                // Function to calculate and update profit
                function updateBookingProfit() {
                    const sellingPrice = parseFloat(document.getElementById('bookingSellingPrice')?.value || 0);
                    const totalCost = parseFloat(document.getElementById('bookingTotalCost')?.value || 0);
                    const profit = sellingPrice - totalCost;

                    if (document.getElementById('bookingProfit')) {
                        document.getElementById('bookingProfit').value = profit.toFixed(2);
                    }
                }

                // Update profit when selling price or total cost changes
                if (document.getElementById('bookingSellingPrice')) {
                    document.getElementById('bookingSellingPrice').addEventListener('input', updateBookingProfit);
                }
                if (document.getElementById('bookingTotalCost')) {
                    document.getElementById('bookingTotalCost').addEventListener('input', updateBookingProfit);
                }

                // Handle booking file form submission
                if (bookingFileForm) {
                    bookingFileForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const formData = new FormData(bookingFileForm);
                        const leadId = formData.get('lead_id');

                        fetch(`${leadsBaseUrl}/${leadId}`, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        ?.content || formData.get('_token'),
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(Object.fromEntries(formData))
                            })
                            .then(response => response.json())
                            .then(data => {
                                alert('Booking file saved successfully!');
                                if (bookingFileModalEl && typeof bootstrap !== 'undefined') {
                                    const modalInstance = bootstrap.Modal.getInstance(bookingFileModalEl);
                                    if (modalInstance) modalInstance.hide();
                                }
                                window.location.reload();
                            })
                            .catch(error => {
                                console.error('Error saving booking file:', error);
                                alert('Unable to save booking file.');
                            });
                    });
                }

                // Initialize Feather icons when booking file modal is shown
                if (bookingFileModalEl) {
                    bookingFileModalEl.addEventListener('shown.bs.modal', () => {
                        safeFeatherReplace(bookingFileModalEl);
                    });
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

                // Re-initialize tooltips after dynamic content is added (e.g., after table updates)
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.addedNodes.length > 0 && typeof bootstrap !== 'undefined' &&
                            bootstrap.Tooltip) {
                            mutation.addedNodes.forEach(function(node) {
                                if (node.nodeType === 1) { // Element node
                                    const tooltips = node.querySelectorAll ? node
                                        .querySelectorAll('[data-bs-toggle="tooltip"]') : [];
                                    tooltips.forEach(function(tooltipEl) {
                                        try {
                                            // Check if tooltip already exists
                                            if (!bootstrap.Tooltip.getInstance(
                                                    tooltipEl)) {
                                                new bootstrap.Tooltip(tooltipEl);
                                            }
                                        } catch (e) {
                                            // Skip if initialization fails
                                        }
                                    });
                                }
                            });
                        }
                    });
                });

                // Start observing the document body for changes
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            });
        </script>
    @endpush
@endsection
