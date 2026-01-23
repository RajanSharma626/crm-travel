@extends('layouts.app')
@section('title', 'Account Booking File | Travel Shravel')
@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <div class="d-flex align-items-center gap-3 flex-grow-1">
                                    <a href="{{ route('accounts.index') }}"
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
                                @can('edit leads')
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal"
                                        data-bs-target="#reassignLeadModal">
                                        <i data-feather="user-check" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Department Assignee
                                    </button>
                                @endcan
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

                                @php
                                    $isViewOnly = $isViewOnly ?? true;
                                    $disabledAttr = $isViewOnly ? 'readonly disabled' : '';
                                    $disabledStyle = $isViewOnly
                                        ? 'style="background-color: #f8f9fa; cursor: not-allowed;"'
                                        : '';
                                @endphp

                                <!-- Customer Details Section -->
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
                                        @php
                                            $paymentState = $customerPaymentState ?? 'none';
                                            $salesBgColor = '#f8f9fa';
                                            $salesBorderColor = '#ced4da';
                                            $paymentIcon = null;
                                            $paymentIconColor = '#6c757d';

                                            if ($paymentState === 'full') {
                                                $salesBgColor = '#d4edda'; // green
                                                $salesBorderColor = '#28a745';
                                                $paymentIcon = 'check-circle';
                                                $paymentIconColor = '#28a745';
                                            } elseif ($paymentState === 'partial') {
                                                $salesBgColor = '#fff3cd'; // yellow
                                                $salesBorderColor = '#ffc107';
                                                $paymentIcon = 'clock';
                                                $paymentIconColor = '#ffc107';
                                            } else {
                                                $salesBgColor = '#f8d7da'; // red
                                                $salesBorderColor = '#dc3545';
                                                $paymentIcon = 'alert-circle';
                                                $paymentIconColor = '#dc3545';
                                            }
                                        @endphp
                                        <div class="col-md-3">
                                            <label class="form-label">Sales Cost</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text"
                                                    value="{{ $lead->selling_price ? number_format($lead->selling_price, 2) : '0.00' }}"
                                                    class="form-control form-control-sm" readonly disabled
                                                    style="background-color: {{ $salesBgColor }}; cursor: not-allowed; border-color: {{ $salesBorderColor }};">
                                                @if ($paymentIcon)
                                                    <span class="input-group-text"
                                                        style="background-color: {{ $salesBgColor }}; border-color: {{ $salesBorderColor }};">
                                                        <i data-feather="{{ $paymentIcon }}"
                                                            style="width: 14px; height: 14px; color: {{ $paymentIconColor }};"></i>
                                                    </span>
                                                @endif
                                            </div>
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



                                <!-- Accounts Summary Box Section -->
                                <div class="mb-4 border rounded-3 p-3">
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                        <i data-feather="file-text" class="me-1"
                                            style="width: 14px; height: 14px;"></i>
                                        Accounts Summary Box
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0" id="accountsSummaryTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Vendor Cost</th>
                                                    <th>Paid Amount</th>
                                                    <th>Payment Status</th>
                                                    <th>Sales Cost</th>
                                                    <th>Received Amount</th>
                                                    <th>Profit Margin</th>
                                                    <th>Payment Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="accountsSummaryTableBody">
                                                @php
                                                    // Calculate Vendor Cost from Vendor Payments (purchase_cost)
                                                    $totalVendorCost =
                                                        isset($vendorPayments) && $vendorPayments->count() > 0
                                                            ? $vendorPayments->sum('purchase_cost')
                                                            : 0;

                                                    // Calculate Paid Amount from Vendor Payments
                                                    $totalPaidAmount =
                                                        isset($vendorPayments) && $vendorPayments->count() > 0
                                                            ? $vendorPayments->sum('paid_amount')
                                                            : 0;

                                                    // Vendor payment status: Done if paid amount >= vendor cost, else Pending
                                                    $vendorPaymentStatus =
                                                        $totalPaidAmount >= $totalVendorCost && $totalVendorCost > 0
                                                            ? 'Done'
                                                            : 'Pending';

                                                    // Sales cost from lead
                                                    $totalSalesCost = $lead->selling_price ?? 0;

                                                    // Received amount from payments
                                                    $totalReceivedAmount = isset($totalReceived)
                                                        ? $totalReceived
                                                        : ($lead->payments
                                                            ? $lead->payments
                                                                ->where('status', 'received')
                                                                ->sum('amount')
                                                            : 0);

                                                    // Customer payment status: Received if received amount >= sales cost, else Pending
                                                    $customerPaymentStatus =
                                                        $totalReceivedAmount >= $totalSalesCost && $totalSalesCost > 0
                                                            ? 'Received'
                                                            : 'Pending';
                                                @endphp
                                                <tr>
                                                    <td>{{ number_format($totalVendorCost, 2) }}</td>
                                                    <td>{{ number_format($totalPaidAmount, 2) }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $vendorPaymentStatus == 'Done' ? 'success' : 'warning' }}">
                                                            {{ $vendorPaymentStatus }}
                                                        </span>
                                                    </td>
                                                    <td>{{ number_format($totalSalesCost, 2) }}</td>
                                                    <td>{{ number_format($totalReceivedAmount, 2) }}</td>
                                                    @php
                                                        $profitMargin = $totalSalesCost - $totalVendorCost;
                                                    @endphp
                                                    <td>
                                                        <span class="fw-bold"
                                                            style="color: {{ $profitMargin >= 0 ? '#28a745' : '#dc3545' }};">
                                                            ₹{{ number_format($profitMargin, 2) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $customerPaymentStatus == 'Received' ? 'success' : 'warning' }}">
                                                            {{ $customerPaymentStatus }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Vendor Payments Section (Accounts - Yellow Part Editable) -->
                                <div class="mb-4 border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="text-uppercase text-muted small fw-semibold mb-0">
                                                <i data-feather="dollar-sign" class="me-1"
                                                    style="width: 14px; height: 14px;"></i>
                                                Vendor Payments (Ops → Accounts)
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0"
                                            id="vendorPaymentsAccountsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Vendor Code/Name</th>
                                                    <th>Booking Type</th>
                                                    <th>Location</th>
                                                    <th>Purchase Cost</th>
                                                    <th>Due Date</th>
                                                    <th>Paid</th>
                                                    <th>Pending</th>
                                                    <th>Payment Mode</th>
                                                    <th>Ref. No.</th>
                                                    <th>Remarks</th>
                                                    <th>Status</th>
                                                    <th>Paid on</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="vendorPaymentsAccountsTableBody">
                                                @if (isset($vendorPayments) && $vendorPayments->count() > 0)
                                                    @foreach ($vendorPayments as $vp)
                                                        <tr data-vendor-payment-id="{{ $vp->id }}">
                                                            <td>{{ $vp->vendor_code ?? '-' }}</td>
                                                            <td>{{ $vp->booking_type ?? '-' }}</td>
                                                            <td>{{ $vp->location ?? '-' }}</td>
                                                            <td>{{ $vp->purchase_cost ? number_format($vp->purchase_cost, 2) : '-' }}
                                                            </td>
                                                            <td>{{ $vp->due_date ? $vp->due_date->format('d/m/Y') : '-' }}
                                                            </td>
                                                            <td>{{ $vp->paid_amount ? number_format($vp->paid_amount, 2) : '-' }}
                                                            </td>
                                                            @php
                                                                $displayPending = null;
                                                                if (isset($vp->purchase_cost)) {
                                                                    $displayPending = max(
                                                                        0,
                                                                        ($vp->purchase_cost ?? 0) -
                                                                            ($vp->paid_amount ?? 0),
                                                                    );
                                                                } else {
                                                                    $displayPending = $vp->pending_amount ?? null;
                                                                }
                                                            @endphp
                                                            <td>{{ $displayPending !== null ? number_format($displayPending, 2) : '-' }}
                                                            </td>
                                                            <td>{{ $vp->payment_mode ?? '-' }}</td>
                                                            <td>{{ $vp->ref_no ?? '-' }}</td>
                                                            <td>{{ $vp->remarks ?? '-' }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $vp->status == 'Paid' ? 'success' : ($vp->status == 'Pending' ? 'warning' : 'secondary') }}">
                                                                    {{ $vp->status ?? 'Pending' }}
                                                                </span>
                                                            </td>
                                                            <td
                                                                data-paid-on="{{ $vp->paid_on ? $vp->paid_on->format('Y-m-d') : '' }}">
                                                                {{ $vp->paid_on ? $vp->paid_on->format('d/m/Y') : '-' }}
                                                            </td>
                                                            <td class="text-center">
                                                                @php
                                                                    $currentUser = Auth::user();
                                                                    $isAccountsUser =
                                                                        $currentUser &&
                                                                        ($currentUser->department === 'Accounts' ||
                                                                            ($currentUser->getRoleNameAttribute() ??
                                                                                '') ===
                                                                                'Accounts' ||
                                                                            (method_exists($currentUser, 'hasRole') &&
                                                                                $currentUser->hasRole('Accounts')) ||
                                                                            (method_exists($currentUser, 'hasRole') &&
                                                                                $currentUser->hasRole(
                                                                                    'Accounts Manager',
                                                                                )));
                                                                    $isAdminUser =
                                                                        $currentUser &&
                                                                        ($currentUser->department === 'Admin' ||
                                                                            (method_exists($currentUser, 'hasRole') &&
                                                                                $currentUser->hasRole('Admin')));
                                                                    $canEditVendorPayment = !(
                                                                        $vp->status === 'Paid' &&
                                                                        !($isAccountsUser || $isAdminUser)
                                                                    );
                                                                @endphp
                                                                @if ($canEditVendorPayment)
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-primary edit-vendor-payment-accounts-btn"
                                                                        data-vendor-payment-id="{{ $vp->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editVendorPaymentAccountsModal">
                                                                        <i data-feather="edit"
                                                                            style="width: 14px; height: 14px;"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="13" class="text-center text-muted py-4">No vendor
                                                            payments found</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Customer Payments (Post Sales → Accounts) -->
                                <div class="mb-4 border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-uppercase text-muted small fw-semibold mb-0">
                                            <i data-feather="credit-card" class="me-1"
                                                style="width: 14px; height: 14px;"></i>
                                            Customer Payments (Post Sales → Accounts)
                                        </h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0" id="customerPaymentsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Amount</th>
                                                    <th>Method</th>
                                                    <th>Paid On</th>
                                                    <th>Due Date</th>
                                                    <th>Transaction ID</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $payments = $lead->payments ?? collect();
                                                @endphp
                                                @if ($payments->count() > 0)
                                                    @foreach ($payments as $payment)
                                                        <tr data-payment-id="{{ $payment->id }}"
                                                            data-notes="{{ $payment->notes ?? '' }}">
                                                            <td>₹{{ number_format($payment->amount, 2) }}</td>
                                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</td>
                                                            <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-' }}
                                                            </td>
                                                            <td>{{ $payment->due_date ? $payment->due_date->format('d/m/Y') : '-' }}
                                                            </td>
                                                            <td>{{ $payment->reference ?? '-' }}</td>
                                                            <td>
                                                                @php
                                                                    $statusColor =
                                                                        $payment->status === 'received'
                                                                            ? 'success'
                                                                            : ($payment->status === 'refunded'
                                                                                ? 'secondary'
                                                                                : 'warning');
                                                                @endphp
                                                                <span class="badge bg-{{ $statusColor }}">
                                                                    {{ ucfirst($payment->status) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $payment->notes ?? '-' }}</td>
                                                            <td class="text-center">
                                                                @php
                                                                    $currentUser = Auth::user();
                                                                    $isPostSalesUser =
                                                                        $currentUser &&
                                                                        ($currentUser->department === 'Post Sales' ||
                                                                            ($currentUser->getRoleNameAttribute() ??
                                                                                '') ===
                                                                                'Post Sales' ||
                                                                            (method_exists($currentUser, 'hasRole') &&
                                                                                $currentUser->hasRole('Post Sales')) ||
                                                                            (method_exists($currentUser, 'hasRole') &&
                                                                                $currentUser->hasRole(
                                                                                    'Post Sales Manager',
                                                                                )));
                                                                @endphp
                                                                @if (!($isPostSalesUser && $payment->status === 'received'))
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-primary edit-customer-payment-btn"
                                                                        data-payment-id="{{ $payment->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editCustomerPaymentModal">
                                                                        <i data-feather="edit"
                                                                            style="width: 14px; height: 14px;"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted py-3">No customer
                                                            payments recorded</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Booking Profitability section removed as Profit Margin added to Accounts Summary --}}


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    <!-- Add/Edit Account Summary Modal -->
    <div class="modal fade" id="addAccountSummaryModal" tabindex="-1" aria-labelledby="addAccountSummaryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountSummaryModalLabel">Add Account Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAccountSummaryForm" method="POST"
                        action="{{ route('accounts.account-summary.store', $lead) }}">
                        @csrf
                        <input type="hidden" id="summaryId" name="summary_id" value="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ref. No.</label>
                                <input type="text" class="form-control form-control-sm" id="modalRefNo"
                                    value="{{ $lead->tsq }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vendor Code/Name</label>
                                <input type="text" class="form-control form-control-sm" id="modalVendorCode"
                                    name="vendor_code" placeholder="Enter vendor code">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vendor Cost</label>
                                <input type="number" class="form-control form-control-sm" id="modalVendorCost"
                                    name="vendor_cost" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Paid Amount</label>
                                <input type="number" class="form-control form-control-sm" id="modalPaidAmount"
                                    name="paid_amount" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vendor Payment Status</label>
                                <select class="form-select form-select-sm" id="modalVendorPaymentStatus"
                                    name="vendor_payment_status">
                                    <option value="">-- Select --</option>
                                    <option value="Done">Done</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Referred By or Lead Source</label>
                                <input type="text" class="form-control form-control-sm" id="modalReferredBy"
                                    name="referred_by" placeholder="Enter referred by">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sales Cost</label>
                                <input type="number" class="form-control form-control-sm" id="modalSalesCost"
                                    name="sales_cost" step="0.01" min="0"
                                    value="{{ $lead->selling_price ?? 0 }}" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Received Amount</label>
                                <input type="number" class="form-control form-control-sm" id="modalReceivedAmount"
                                    name="received_amount" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer Payment Status</label>
                                <select class="form-select form-select-sm" id="modalCustomerPaymentStatus"
                                    name="customer_payment_status">
                                    <option value="">-- Select --</option>
                                    <option value="Received">Received</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="addAccountSummaryForm">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Vendor Payment Modal (Accounts - Yellow Part Only) -->
    <div class="modal fade" id="editVendorPaymentAccountsModal" tabindex="-1"
        aria-labelledby="editVendorPaymentAccountsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVendorPaymentAccountsModalLabel">Edit Vendor Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editVendorPaymentAccountsForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="vendorPaymentAccountsId" name="vendor_payment_id" value="">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Paid Amount</label>
                                <input type="number" class="form-control form-control-sm" id="accountsModalPaidAmount"
                                    name="paid_amount" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pending Amount</label>
                                <input type="number" class="form-control form-control-sm"
                                    id="accountsModalPendingAmount" name="pending_amount" step="0.01" min="0"
                                    placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Mode</label>
                                <select class="form-select form-select-sm" id="accountsModalPaymentMode"
                                    name="payment_mode">
                                    <option value="">-- Select --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="NEFT">NEFT</option>
                                    <option value="RTGS">RTGS</option>
                                    <option value="WIB">WIB</option>
                                    <option value="Online">Online</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ref. No.</label>
                                <input type="text" class="form-control form-control-sm" id="accountsModalRefNo"
                                    name="ref_no" placeholder="Payment Reference No.">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="accountsModalStatus" name="status"
                                    required>
                                    <option value="">-- Select --</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Paid on</label>
                                <input type="date" class="form-control form-control-sm" id="accountsModalPaidOn"
                                    name="paid_on">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control form-control-sm" id="accountsModalRemarks" name="remarks" rows="2"
                                    placeholder="Enter remarks"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="editVendorPaymentAccountsForm">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Customer Payment Modal -->
    <div class="modal fade" id="editCustomerPaymentModal" tabindex="-1" aria-labelledby="editCustomerPaymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerPaymentModalLabel">Edit Customer Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCustomerPaymentForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="customerPaymentId" name="payment_id" value="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-sm" id="customerPaymentAmount"
                                    name="amount" step="0.01" min="0" required placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Method <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="customerPaymentMethod" name="method"
                                    required>
                                    <option value="">-- Select --</option>
                                    <option value="">Select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="NEFT">NEFT</option>
                                    <option value="RTGS">RTGS</option>
                                    <option value="WIB">WIB</option>
                                    <option value="Online">Online</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Paid On <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" id="customerPaymentDate"
                                    name="payment_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control form-control-sm" id="customerPaymentDueDate"
                                    name="due_date">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Transaction ID</label>
                                <input type="text" class="form-control form-control-sm" id="customerPaymentReference"
                                    name="reference" placeholder="Transaction Reference No.">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="customerPaymentStatus" name="status"
                                    required>
                                    <option value="pending">Pending</option>
                                    <option value="received">Received</option>
                                    <option value="To Be Refund">To Be Refund</option>
                                    <option value="Refund Passed">Refund Passed</option>

                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control form-control-sm" id="customerPaymentNotes" name="notes" rows="2"
                                    placeholder="Enter remarks"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="editCustomerPaymentForm">Save</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const leadId = {{ $lead->id }};
                let currentEditSummaryId = null;

                // Initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                // Reset modal form
                function resetAccountSummaryModal() {
                    const form = document.getElementById('addAccountSummaryForm');
                    form.reset();
                    document.getElementById('summaryId').value = '';
                    document.getElementById('modalRefNo').value = '{{ $lead->tsq }}';
                    document.getElementById('addAccountSummaryModalLabel').textContent = 'Add Account Summary';
                    // Reset form action to create mode
                    form.action = '{{ route('accounts.account-summary.store', $lead) }}';
                    // Remove PUT method if exists
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) {
                        methodInput.remove();
                    }
                    currentEditSummaryId = null;
                }

                // Handle modal show event
                const addAccountSummaryModal = document.getElementById('addAccountSummaryModal');
                if (addAccountSummaryModal) {
                    addAccountSummaryModal.addEventListener('show.bs.modal', function() {
                        resetAccountSummaryModal();
                    });
                }

                // Handle edit button click
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.edit-account-summary-btn')) {
                        const btn = e.target.closest('.edit-account-summary-btn');
                        const summaryId = btn.dataset.summaryId;
                        const row = btn.closest('tr');

                        currentEditSummaryId = summaryId;
                        document.getElementById('summaryId').value = summaryId;
                        document.getElementById('addAccountSummaryModalLabel').textContent =
                            'Edit Account Summary';

                        // Update form action for edit mode
                        const form = document.getElementById('addAccountSummaryForm');
                        form.action = `/accounts/${leadId}/account-summary/${summaryId}`;
                        // Ensure PUT method is set
                        if (!form.querySelector('input[name="_method"]')) {
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'PUT';
                            form.appendChild(methodInput);
                        }

                        // Populate form with row data
                        document.getElementById('modalRefNo').value = row.cells[0].textContent.trim();
                        document.getElementById('modalVendorCode').value = row.cells[1].textContent.trim() !==
                            '-' ? row.cells[1].textContent.trim() : '';
                        document.getElementById('modalVendorCost').value = row.cells[2].textContent.trim() !==
                            '-' ? row.cells[2].textContent.trim().replace(/[₹,]/g, '') : '';
                        document.getElementById('modalPaidAmount').value = row.cells[3].textContent.trim() !==
                            '-' ? row.cells[3].textContent.trim().replace(/[₹,]/g, '') : '';
                        document.getElementById('modalVendorPaymentStatus').value = row.cells[4].querySelector(
                            '.badge') ? row.cells[4].querySelector('.badge').textContent.trim() : '';
                        document.getElementById('modalReferredBy').value = row.cells[5].textContent.trim() !==
                            '-' ? row.cells[5].textContent.trim() : '';
                        document.getElementById('modalSalesCost').value = row.cells[6].textContent.trim() !==
                            '-' ? row.cells[6].textContent.trim().replace(/[₹,]/g, '') : '';
                        document.getElementById('modalReceivedAmount').value = row.cells[7].textContent
                            .trim() !== '-' ? row.cells[7].textContent.trim().replace(/[₹,]/g, '') : '';
                        document.getElementById('modalCustomerPaymentStatus').value = row.cells[8]
                            .querySelector('.badge') ? row.cells[8].querySelector('.badge').textContent.trim() :
                            '';
                    }
                });

                // Handle account summary form - update action for edit mode
                const addAccountSummaryForm = document.getElementById('addAccountSummaryForm');
                if (addAccountSummaryForm) {
                    addAccountSummaryForm.addEventListener('submit', function(e) {
                        const summaryId = document.getElementById('summaryId').value;
                        if (summaryId) {
                            // Edit mode - update form action and method
                            this.action = `/accounts/${leadId}/account-summary/${summaryId}`;
                            // Add PUT method
                            if (!this.querySelector('input[name="_method"]')) {
                                const methodInput = document.createElement('input');
                                methodInput.type = 'hidden';
                                methodInput.name = '_method';
                                methodInput.value = 'PUT';
                                this.appendChild(methodInput);
                            }
                        }
                        // Form will submit normally
                    });
                }

                // Handle delete button click
                document.addEventListener('click', async function(e) {
                    if (e.target.closest('.delete-account-summary-btn')) {
                        const btn = e.target.closest('.delete-account-summary-btn');
                        const summaryId = btn.dataset.summaryId;

                        if (!confirm('Are you sure you want to delete this account summary?')) {
                            return;
                        }

                        try {
                            const response = await fetch(
                                `/accounts/${leadId}/account-summary/${summaryId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]')?.content
                                    }
                                });

                            const data = await response.json();

                            if (!response.ok) {
                                throw new Error(data.message || 'Failed to delete account summary');
                            }

                            // Reload page to show updated data
                            window.location.reload();
                        } catch (error) {
                            alert(error.message || 'Failed to delete account summary');
                        }
                    }
                });

                // Re-initialize feather icons when modal is shown
                if (addAccountSummaryModal) {
                    addAccountSummaryModal.addEventListener('shown.bs.modal', function() {
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    });
                }

                // Vendor Payment Accounts handlers
                let currentEditVendorPaymentId = null;

                // Handle edit vendor payment button click
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.edit-vendor-payment-accounts-btn')) {
                        const btn = e.target.closest('.edit-vendor-payment-accounts-btn');
                        const vendorPaymentId = btn.dataset.vendorPaymentId;
                        const row = btn.closest('tr');

                        currentEditVendorPaymentId = vendorPaymentId;
                        document.getElementById('vendorPaymentAccountsId').value = vendorPaymentId;

                        // Set form action
                        const form = document.getElementById('editVendorPaymentAccountsForm');
                        form.action = `/accounts/${leadId}/vendor-payment/${vendorPaymentId}`;

                        // Populate editable fields
                        document.getElementById('accountsModalPaidAmount').value = row.cells[5].textContent
                            .trim() !== '-' ? row.cells[5].textContent.trim().replace(/[₹,]/g, '') : '';
                        document.getElementById('accountsModalPendingAmount').value = row.cells[6].textContent
                            .trim() !== '-' ? row.cells[6].textContent.trim().replace(/[₹,]/g, '') : '';
                        document.getElementById('accountsModalPaymentMode').value = row.cells[7].textContent
                            .trim() !== '-' ? row.cells[7].textContent.trim() : '';
                        document.getElementById('accountsModalRefNo').value = row.cells[8].textContent
                            .trim() !== '-' ? row.cells[8].textContent.trim() : '';
                        document.getElementById('accountsModalRemarks').value = row.cells[9].textContent
                            .trim() !== '-' ? row.cells[9].textContent.trim() : '';

                        // Populate status from badge
                        const statusBadge = row.cells[10].querySelector('.badge');
                        const currentStatus = statusBadge ? statusBadge.textContent.trim() : 'Pending';
                        document.getElementById('accountsModalStatus').value = currentStatus === 'Paid' ?
                            'Paid' : 'Pending';

                        // Populate paid_on date if available (cell index 11)
                        const paidOnCell = row.cells[11];
                        if (paidOnCell) {
                            const paidOnValue = paidOnCell.getAttribute('data-paid-on') || '';
                            if (paidOnValue) {
                                document.getElementById('accountsModalPaidOn').value = paidOnValue;
                            } else {
                                document.getElementById('accountsModalPaidOn').value = '';
                            }
                        } else {
                            document.getElementById('accountsModalPaidOn').value = '';
                        }
                    }
                });


                // Reset modal on close
                const editVendorPaymentAccountsModal = document.getElementById('editVendorPaymentAccountsModal');
                if (editVendorPaymentAccountsModal) {
                    editVendorPaymentAccountsModal.addEventListener('hidden.bs.modal', function() {
                        document.getElementById('editVendorPaymentAccountsForm').reset();
                        document.getElementById('accountsModalPaidOn').value = '';
                        currentEditVendorPaymentId = null;
                    });
                }

                // Handle edit customer payment button click
                let currentEditCustomerPaymentId = null;
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.edit-customer-payment-btn')) {
                        const btn = e.target.closest('.edit-customer-payment-btn');
                        const paymentId = btn.dataset.paymentId;
                        const row = btn.closest('tr');

                        currentEditCustomerPaymentId = paymentId;
                        document.getElementById('customerPaymentId').value = paymentId;

                        // Set form action
                        const form = document.getElementById('editCustomerPaymentForm');
                        form.action = `/leads/${leadId}/payments/${paymentId}`;

                        // Populate form fields from table row
                        const amount = row.cells[0].textContent.trim().replace(/[₹,]/g, '');
                        const methodDisplay = row.cells[1].textContent.trim();
                        const paidOn = row.cells[2].textContent.trim();
                        const dueDate = row.cells[3].textContent.trim();
                        const reference = row.cells[4].textContent.trim();
                        const statusBadge = row.cells[5].querySelector('.badge');
                        const status = statusBadge ? statusBadge.textContent.trim().toLowerCase() : 'pending';
                        // Get notes from table cell (index 6) or data attribute as fallback
                        const notes = row.cells[6] ? row.cells[6].textContent.trim() : (row.dataset.notes ||
                            '');

                        document.getElementById('customerPaymentAmount').value = amount !== '-' ? amount : '';

                        document.getElementById('customerPaymentMethod').value = methodDisplay;

                        // Convert date from d/m/Y to Y-m-d format
                        if (paidOn !== '-') {
                            const [day, month, year] = paidOn.split('/');
                            document.getElementById('customerPaymentDate').value = `${year}-${month}-${day}`;
                        }

                        if (dueDate !== '-') {
                            const [day, month, year] = dueDate.split('/');
                            document.getElementById('customerPaymentDueDate').value = `${year}-${month}-${day}`;
                        }

                        document.getElementById('customerPaymentReference').value = reference !== '-' ?
                            reference : '';
                        document.getElementById('customerPaymentStatus').value = status;
                        document.getElementById('customerPaymentNotes').value = notes || '';
                    }
                });


                // Reset customer payment modal on close
                const editCustomerPaymentModal = document.getElementById('editCustomerPaymentModal');
                if (editCustomerPaymentModal) {
                    editCustomerPaymentModal.addEventListener('hidden.bs.modal', function() {
                        document.getElementById('editCustomerPaymentForm').reset();
                        document.getElementById('customerPaymentNotes').value = '';
                        currentEditCustomerPaymentId = null;
                    });
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
                                        <label class="form-label">Accountant</label>
                                        <select name="accounts_assignee" class="form-select form-select-sm">
                                            <option value="">Select Accountant</option>
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
