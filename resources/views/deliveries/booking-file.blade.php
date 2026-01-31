@extends('layouts.app')
@section('title', 'Delivery Booking File | Travel Shravel')
@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <div class="d-flex align-items-center gap-3 flex-grow-1">
                                    <a href="{{ $backUrl ?? route('deliveries.index') }}"
                                        class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover">
                                        <span class="icon">
                                            <span class="feather-icon">
                                                <i data-feather="arrow-left"></i>
                                            </span>
                                        </span>
                                    </a>
                                    <div>
                                        <h1 class="mb-0">Booking File</h1>
                                        <p class="text-muted mb-0 small">TSQ: {{ $lead->tsq }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @php
                                        // Enable buttons if operation exists
                                        $hasOperation = $lead->operation !== null;
                                    @endphp

                                    <button type="button"
                                        class="btn btn-sm btn-outline-success {{ !$hasOperation ? 'disabled' : '' }}"
                                        onclick="openDownloadModal('{{ route('deliveries.download-voucher', ['lead' => $lead, 'type' => 'itinerary']) }}')"
                                        @if (!$hasOperation) disabled style="opacity: 0.6;" @endif>
                                        <i data-feather="download" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Itinerary
                                    </button>

                                    <button type="button"
                                        class="btn btn-sm btn-outline-success {{ !$hasOperation ? 'disabled' : '' }}"
                                        onclick="openDownloadModal('{{ route('deliveries.download-voucher', ['lead' => $lead, 'type' => 'service-voucher']) }}')"
                                        @if (!$hasOperation) disabled style="opacity: 0.6;" @endif>
                                        <i data-feather="download" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Service
                                    </button>

                                    @can('edit leads')
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#reassignLeadModal">
                                            <i data-feather="user-check" class="me-1" style="width: 14px; height: 14px;"></i>
                                            Department Assignee
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </header>

                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>There were some problems with your submission:</strong>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <!-- Customer Details Section (View Mode) -->
                                <div class="mb-4 border rounded-3 p-3">
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                        <i data-feather="user" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Customer Details
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Ref No.</label>
                                            <input type="text" value="{{ $lead->tsq }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Salutation</label>
                                            <input type="text" value="{{ $lead->salutation ?? '' }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">First Name</label>
                                            <input type="text" value="{{ $lead->first_name }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" value="{{ $lead->last_name ?? '' }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Primary No.</label>
                                            <input type="text" value="{{ $lead->primary_phone ?? $lead->phone }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Secondary No.</label>
                                            <input type="text" value="{{ $lead->secondary_phone ?? '' }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Emergency No.</label>
                                            <input type="text" value="{{ $lead->other_phone ?? '' }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Email ID</label>
                                            <input type="email" value="{{ $lead->email }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">No. of Adult(s)</label>
                                            <input type="number" value="{{ $lead->adults }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Child (2-5 years)</label>
                                            <input type="number" value="{{ $lead->children_2_5 ?? 0 }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Child (6-11 years)</label>
                                            <input type="number" value="{{ $lead->children_6_11 ?? 0 }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Infant (>2 years)</label>
                                            <input type="number" value="{{ $lead->infants ?? 0 }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Travel Date</label>
                                            <input type="text"
                                                value="{{ $lead->travel_date ? $lead->travel_date->format('d M, Y') : 'N/A' }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Return Date</label>
                                            <input type="text"
                                                value="{{ $lead->return_date ? $lead->return_date->format('d M, Y') : 'N/A' }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Booked On</label>
                                            <input type="text"
                                                value="{{ $lead->booked_on ? $lead->booked_on->format('d M, Y h:i A') : 'N/A' }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Sales Cost</label>
                                            <input type="text"
                                                value="{{ $lead->selling_price ? number_format($lead->selling_price, 2) : '0.00' }}"
                                                class="form-control form-control-sm" readonly disabled
                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        @php
                                            $stageInfo = $stageInfo ?? null;
                                            $currentStage = $currentStage ?? 'Pending';
                                        @endphp
                                        @if ($stageInfo)
                                            <div class="col-md-3">
                                                <label class="form-label">Stage</label>
                                                <div class="input-group input-group-sm">
                                                    <select name="stage" id="stageSelect"
                                                        class="form-select form-control-sm">
                                                        @foreach ($stageInfo['stages'] as $stage)
                                                            <option value="{{ $stage }}"
                                                                {{ $currentStage == $stage ? 'selected' : '' }}>
                                                                {{ $stage }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        id="updateStageBtn">
                                                        Update
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Remarks Section -->
                                <div class="mb-4 border rounded-3 p-3">
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                        <i data-feather="message-circle" class="me-1"
                                            style="width: 14px; height: 14px;"></i>
                                        Remarks
                                    </h6>

                                    <!-- Add Remark Form -->
                                    <form method="POST" action="{{ route('leads.booking-file-remarks.store', $lead) }}">
                                        @csrf
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-9">
                                                <label class="form-label">Remark <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="remark" class="form-control form-control-sm" rows="2" required
                                                    placeholder="Enter your remark..."></textarea>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                                    <i data-feather="save" style="width: 14px; height: 14px;"></i>
                                                    Add Remark
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Destination Section (View Mode) -->
                                <div class="mb-4 border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-uppercase text-muted small fw-semibold mb-0">
                                            <i data-feather="map-pin" class="me-1"
                                                style="width: 14px; height: 14px;"></i>
                                            Destination
                                        </h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 15%;">Destination</th>
                                                    <th style="width: 15%;">Location</th>
                                                    <th style="width: 12%;" class="text-center">Only Hotel</th>
                                                    <th style="width: 12%;" class="text-center">Only TT</th>
                                                    <th style="width: 12%;" class="text-center">Hotel + TT</th>
                                                    <th style="width: 10%;">From Date</th>
                                                    <th style="width: 10%;">To Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($lead->bookingDestinations && $lead->bookingDestinations->count() > 0)
                                                    @foreach ($lead->bookingDestinations as $bd)
                                                        <tr>
                                                            <td>{{ $bd->destination }}</td>
                                                            <td>{{ $bd->location }}</td>
                                                            <td class="text-center">
                                                                @if ($bd->only_hotel)
                                                                    <i data-feather="check"
                                                                        style="width: 16px; height: 16px; color: #28a745;"></i>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if ($bd->only_tt)
                                                                    <i data-feather="check"
                                                                        style="width: 16px; height: 16px; color: #28a745;"></i>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if ($bd->hotel_tt)
                                                                    <i data-feather="check"
                                                                        style="width: 16px; height: 16px; color: #28a745;"></i>
                                                                @endif
                                                            </td>
                                                            <td>{{ $bd->from_date ? $bd->from_date->format('d/m/Y') : '' }}
                                                            </td>
                                                            <td>{{ $bd->to_date ? $bd->to_date->format('d/m/Y') : '' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-4">
                                                            <i data-feather="inbox"
                                                                style="width: 24px; height: 24px; opacity: 0.5;"
                                                                class="mb-2"></i>
                                                            <div>no records found</div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Accommodation Details Section (View Mode) -->
                                <div class="mb-4 border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-uppercase text-muted small fw-semibold mb-0">
                                            <i data-feather="home" class="me-1" style="width: 14px; height: 14px;"></i>
                                            Accommodation Details
                                        </h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 12%;">Destination</th>
                                                    <th style="width: 12%;">Location</th>
                                                    <th style="width: 12%;">Stay At</th>
                                                    <th style="width: 10%;">Check-in</th>
                                                    <th style="width: 10%;">Check-out</th>
                                                    <th style="width: 15%;">Room Type</th>
                                                    <th style="width: 15%;">Meal Plan</th>
                                                    <th style="width: 14%;" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($lead->bookingAccommodations && $lead->bookingAccommodations->count() > 0)
                                                    @foreach ($lead->bookingAccommodations as $ba)
                                                        <tr>
                                                            <td>{{ $ba->destination }}</td>
                                                            <td>{{ $ba->location }}</td>
                                                            <td>{{ $ba->stay_at }}</td>
                                                            <td>{{ $ba->checkin_date ? $ba->checkin_date->format('d/m/Y') : '' }}
                                                            </td>
                                                            <td>{{ $ba->checkout_date ? $ba->checkout_date->format('d/m/Y') : '' }}
                                                            </td>
                                                            <td>{{ $ba->room_type }}</td>
                                                            <td>{{ $ba->meal_plan }}</td>
                                                            <td class="text-center">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-success {{ !($hasOperation && $lead->vouchers()->where('voucher_type', 'accommodation')->where('accommodation_id', $ba->id)->exists()) ? 'disabled' : '' }}"
                                                                    onclick="openDownloadModal('{{ route('deliveries.download-accommodation-voucher', ['lead' => $lead, 'accommodation' => $ba->id]) }}')"
                                                                    title="Download Accommodation Voucher"
                                                                    @if (
                                                                        !(
                                                                            $hasOperation &&
                                                                            $lead->vouchers()->where('voucher_type', 'accommodation')->where('accommodation_id', $ba->id)->exists()
                                                                        )) disabled style="opacity: 0.6;" @endif>
                                                                    <i data-feather="download"
                                                                        style="width: 16px; height: 16px;"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted py-4">
                                                            <i data-feather="inbox"
                                                                style="width: 24px; height: 24px; opacity: 0.5;"
                                                                class="mb-2"></i>
                                                            <div>no records found</div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Arrival/Departure Details Section (View Mode) -->
                                <div class="mb-4 border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-uppercase text-muted small fw-semibold mb-0">
                                            <i data-feather="navigation" class="me-1"
                                                style="width: 14px; height: 14px;"></i>
                                            Arrival/Departure Details
                                        </h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0 text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 12%;" rowspan="2">Mode</th>
                                                    <th style="width: 15%;" rowspan="2">Info</th>
                                                    <th style="width: 12%;" rowspan="2">From City</th>
                                                    <th style="width: 12%;" rowspan="2">To City</th>
                                                    <th colspan="2" style="width: 18%;">Dep Date & Time</th>
                                                    <th colspan="2" style="width: 18%;">Arrival Date & Time</th>
                                                </tr>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $allTransports = $lead->bookingArrivalDepartures ?? collect();
                                                @endphp
                                                @if ($allTransports && $allTransports->count() > 0)
                                                    @foreach ($allTransports as $transport)
                                                        <tr>
                                                            <td>{{ $transport->mode }}</td>
                                                            <td>{{ $transport->info }}</td>
                                                            <td>{{ $transport->from_city }}</td>
                                                            <td>{{ $transport->to_city ?? '' }}</td>
                                                            <td>
                                                                {{ $transport->departure_date ? ($transport->departure_date instanceof \DateTime ? $transport->departure_date->format('d/m/Y') : date('d/m/Y', strtotime($transport->departure_date))) : '' }}
                                                            </td>
                                                            <td>
                                                                {{ $transport->departure_time ? substr($transport->departure_time, 0, 5) : '' }}
                                                            </td>
                                                            <td>
                                                                {{ $transport->arrival_date ? ($transport->arrival_date instanceof \DateTime ? $transport->arrival_date->format('d/m/Y') : date('d/m/Y', strtotime($transport->arrival_date))) : '' }}
                                                            </td>
                                                            <td>
                                                                {{ $transport->arrival_time ? substr($transport->arrival_time, 0, 5) : '' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted py-4">
                                                            <i data-feather="inbox"
                                                                style="width: 24px; height: 24px; opacity: 0.5;"
                                                                class="mb-2"></i>
                                                            <div>no records found</div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Day-Wise Itinerary Section (View Mode) -->
                                <div class="mb-4 border rounded-3 p-3" id="dayWiseItinerarySection">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-uppercase text-muted small fw-semibold mb-0">
                                            <i data-feather="calendar" class="me-1"
                                                style="width: 14px; height: 14px;"></i>
                                            Day-Wise Itinerary
                                        </h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 12%;">Day & Date</th>
                                                    <th style="width: 8%;">Time</th>
                                                    <th style="width: 10%;">Location</th>
                                                    <th style="width: 20%;">Activity/Tour Description</th>
                                                    <th style="width: 10%;">Stay at</th>
                                                    <th style="width: 15%;">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($lead->bookingItineraries && $lead->bookingItineraries->count() > 0)
                                                    @foreach ($lead->bookingItineraries as $bi)
                                                        <tr>
                                                            <td>{{ $bi->day_and_date }}</td>
                                                            <td>{{ $bi->time ? substr($bi->time, 0, 5) : '' }}</td>
                                                            <td>{{ $bi->location }}</td>
                                                            <td>
                                                                @if ($bi->activity_tour_description)
                                                                    {!! $bi->activity_tour_description !!}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>{{ $bi->stay_at }}</td>
                                                            <td>{{ $bi->remarks }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">
                                                            <i data-feather="inbox"
                                                                style="width: 24px; height: 24px; opacity: 0.5;"
                                                                class="mb-2"></i>
                                                            <div>no records found</div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                

                                <!-- History Section (Remark History) -->
                                <div class="mb-4 border rounded-3 p-3">
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                        <i data-feather="clock" class="me-1" style="width: 14px; height: 14px;"></i>
                                        History
                                    </h6>
                                    <div style="max-height: 400px; overflow-y: auto;">
                                        @php
                                            $lead->load('bookingFileRemarks.user');
                                            // Check if user is admin
                                            $isAdmin =
                                                Auth::user()->hasRole('Admin') ||
                                                Auth::user()->hasRole('Developer') ||
                                                Auth::user()->department === 'Admin';
                                            // If admin, show all remarks; otherwise apply visibility rules:
                                            // - Sales users: show only their own remarks
                                            // - Other departments: show Sales remarks + their own remarks
                                            $remarksQuery = $lead->bookingFileRemarks();
                                            $currentUser = Auth::user();

                                            if ($isAdmin) {
                                                $visibleRemarks = $remarksQuery->orderBy('created_at', 'desc')->get();
                                            } else {
                                                $currentDept = $currentUser->department ?? '';
                                                if ($currentDept === 'Sales') {
                                                    // Sales users see only their own remarks
                                                    $visibleRemarks = $remarksQuery->where('user_id', $currentUser->id)
                                                        ->orderBy('created_at', 'desc')->get();
                                                } else {
                                                    // Other departments see remarks made by Sales + their own remarks
                                                    $visibleRemarks = $remarksQuery->where(function ($q) use ($currentUser) {
                                                        $q->whereHas('user', function ($uq) {
                                                            $uq->where('department', 'Sales');
                                                        })->orWhere('user_id', $currentUser->id);
                                                    })->orderBy('created_at', 'desc')->get();
                                                }
                                            }
                                        @endphp
                                        @if ($visibleRemarks->count() > 0)
                                            <div class="timeline">
                                                @foreach ($visibleRemarks as $remark)
                                                    <div class="border rounded-3 p-3 mb-3 bg-white">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div class="d-flex align-items-start flex-grow-1">
                                                                <div class="avatar avatar-rounded rounded-circle me-3 flex-shrink-0"
                                                                    style="background-color: #007d88; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                                    <span class="text-white fw-bold"
                                                                        style="font-size: 0.875rem;">
                                                                        {{ strtoupper(substr($remark->user->name ?? 'U', 0, 1)) }}
                                                                    </span>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                                        <strong
                                                                            class="text-dark">{{ $remark->user->name ?? 'Unknown' }}</strong>
                                                                        <small
                                                                            class="text-muted">{{ $remark->created_at->format('d M, Y h:i A') }}</small>
                                                                    </div>
                                                                    <p class="mb-0 text-dark" style="line-height: 1.6;">
                                                                        {{ $remark->remark }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted text-center mb-0 py-4">
                                                <i data-feather="message-circle" class="me-2"
                                                    style="width: 16px; height: 16px;"></i>
                                                no records found
                                            </p>
                                        @endif
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

    <!-- Download Options Modal -->
    <div class="modal fade" id="downloadOptionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title mx-auto">Download Option</h5>
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4 p-4">
                    <input type="hidden" id="downloadBaseUrl">
                    <div class="row g-3">
                        <div class="col-6">
                            <button type="button"
                                class="btn btn-outline-primary w-100 h-100 py-4 d-flex flex-column align-items-center justify-content-center gap-2 shadow-sm"
                                onclick="downloadWithOptions('wcd')">
                                <i data-feather="check-circle" style="width: 32px; height: 32px;"></i>
                                <span class="fw-bold fs-5 mt-1">WCD</span>
                                <span class="small text-muted">With Company Details</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button"
                                class="btn btn-outline-danger w-100 h-100 py-4 d-flex flex-column align-items-center justify-content-center gap-2 shadow-sm"
                                onclick="downloadWithOptions('ncd')">
                                <i data-feather="slash" style="width: 32px; height: 32px;"></i>
                                <span class="fw-bold fs-5 mt-1">NCD</span>
                                <span class="small text-muted">No Company Details</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openDownloadModal(url) {
                document.getElementById('downloadBaseUrl').value = url;
                const modal = new bootstrap.Modal(document.getElementById('downloadOptionsModal'));
                modal.show();

                // Re-initialize icons inside modal if needed, though they are likely static
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }

            function downloadWithOptions(option) {
                const baseUrl = document.getElementById('downloadBaseUrl').value;
                const withCompanyDetails = option === 'wcd' ? '1' : '0';
                const separator = baseUrl.includes('?') ? '&' : '?';
                const url = `${baseUrl}${separator}with_company_details=${withCompanyDetails}`;

                window.open(url, '_blank');

                const modalEl = document.getElementById('downloadOptionsModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            }
        </script>
        <script>
            $(document).ready(function() {
                // Initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                // Handle Stage Update Button
                const updateStageBtn = document.getElementById('updateStageBtn');
                const stageSelect = document.getElementById('stageSelect');

                if (updateStageBtn && stageSelect) {
                    updateStageBtn.addEventListener('click', async function() {
                        const selectedStage = stageSelect.value;

                        if (!selectedStage) {
                            alert('Please select a stage');
                            return;
                        }

                        const originalText = updateStageBtn.textContent;
                        updateStageBtn.disabled = true;
                        updateStageBtn.textContent = 'Updating...';

                        try {
                            const response = await fetch(
                                '{{ route('leads.update-stage', $lead) }}', {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]')?.content,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        stage: selectedStage
                                    })
                                });

                            const result = await response.json();

                            if (response.ok) {
                                alert(result.message || 'Stage updated successfully!');
                                // Optionally reload the page to reflect changes
                                window.location.reload();
                            } else {
                                alert(result.message || 'Error updating stage');
                                updateStageBtn.disabled = false;
                                updateStageBtn.textContent = originalText;
                            }
                        } catch (error) {
                            console.error('Error updating stage:', error);
                            alert('An unexpected error occurred while updating stage');
                            updateStageBtn.disabled = false;
                            updateStageBtn.textContent = originalText;
                        }
                    });
                }
            });
        </script>

        @can('edit leads')
            <!-- Re-assign Lead Modal -->
            <!-- Re-assign Lead Modal -->
            <div class="modal fade" id="reassignLeadModal" tabindex="-1" aria-labelledby="reassignLeadModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('leads.reassign', $lead) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="reassignLeadModalLabel">Department Assignee</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    {{-- Sales (Main Assignee) --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Sales</label>
                                        <select name="reassigned_employee_id" class="form-select form-select-sm" required>
                                            <option value="">Select Sales Agent</option>
                                            @foreach ($employees->filter(fn($e) => in_array($e->department, ['Sales', 'Admin']) || in_array($e->role, ['Sales', 'Sales Manager'])) as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ $lead->assigned_user_id == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Post Sales --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Post Sales</label>
                                        <select name="post_sales_assignee" class="form-select form-select-sm">
                                            <option value="">Select Post Sales Agent</option>
                                            @foreach ($employees->filter(fn($e) => $e->department === 'Post Sales' || in_array($e->role, ['Post Sales', 'Post Sales Manager'])) as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ $lead->post_sales_user_id == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Operations --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Operations</label>
                                        <select name="operations_assignee" class="form-select form-select-sm">
                                            <option value="">Select Operations Agent</option>
                                            @foreach ($employees->filter(fn($e) => $e->department === 'Operation' || in_array($e->role, ['Operation', 'Operation Manager'])) as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ $lead->operations_user_id == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Ticketing --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Ticketing</label>
                                        <select name="ticketing_assignee" class="form-select form-select-sm">
                                            <option value="">Select Ticketing Agent</option>
                                            @foreach ($employees->filter(fn($e) => $e->department === 'Ticketing' || $e->role === 'Ticketing') as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ $lead->ticketing_user_id == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Visa --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Visa</label>
                                        <select name="visa_assignee" class="form-select form-select-sm">
                                            <option value="">Select Visa Agent</option>
                                            @foreach ($employees->filter(fn($e) => $e->department === 'Visa' || $e->role === 'Visa') as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ $lead->visa_user_id == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Insurance --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Insurance</label>
                                        <select name="insurance_assignee" class="form-select form-select-sm">
                                            <option value="">Select Insurance Agent</option>
                                            @foreach ($employees->filter(fn($e) => $e->department === 'Insurance' || $e->role === 'Insurance') as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ $lead->insurance_user_id == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Accountant (Accounts) --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Accounts</label>
                                        <select name="accounts_assignee" class="form-select form-select-sm">
                                            <option value="">Select Accounts</option>
                                            @foreach ($employees->filter(fn($e) => $e->department === 'Accounts' || in_array($e->role, ['Accounts', 'Accounts Manager'])) as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ $lead->accounts_user_id == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Delivery --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Delivery</label>
                                        <select name="delivery_assignee" class="form-select form-select-sm">
                                            <option value="">Select Delivery Agent</option>
                                            @foreach ($employees->filter(fn($e) => $e->department === 'Delivery' || in_array($e->role, ['Delivery', 'Delivery Manager'])) as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ $lead->delivery_user_id == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    @endpush
@endsection
