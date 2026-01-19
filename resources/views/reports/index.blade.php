@extends('layouts.app')
@section('title', 'Dashboard | Travel Shravel')
@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Dashboard</h1>
                            </div>
                        </header>

                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                <!-- Statistics Cards -->
                                <div class="row mb-4">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-primary">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-icon avatar-lg avatar-light-primary">
                                                            <span class="avatar-icon"><i data-feather="users"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="text-muted fs-6">Total Leads</div>
                                                        <div class="fs-4 fw-semibold">{{ $stats['total_leads'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-success">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-icon avatar-lg avatar-light-success">
                                                            <span class="avatar-icon"><i
                                                                    data-feather="check-circle"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="text-muted fs-6">Booked Leads</div>
                                                        <div class="fs-4 fw-semibold">{{ $stats['booked_leads'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-info">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-icon avatar-lg avatar-light-info">
                                                            <span class="avatar-icon"><i
                                                                    data-feather="dollar-sign"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="text-muted fs-6">Total Revenue</div>
                                                        <div class="fs-4 fw-semibold">
                                                            ₹{{ number_format($stats['total_revenue'], 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-icon avatar-lg avatar-light-warning">
                                                            <span class="avatar-icon"><i data-feather="package"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="text-muted fs-6">Pending Deliveries</div>
                                                        <div class="fs-4 fw-semibold">{{ $stats['pending_deliveries'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Stats -->
                                <div class="row mb-4">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-danger">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-icon avatar-lg avatar-light-danger">
                                                            <span class="avatar-icon"><i
                                                                    data-feather="alert-circle"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="text-muted fs-6">New Leads</div>
                                                        <div class="fs-4 fw-semibold">{{ $stats['new_leads'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-secondary">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-icon avatar-lg avatar-light-secondary">
                                                            <span class="avatar-icon"><i data-feather="x-circle"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="text-muted fs-6">Closed Leads</div>
                                                        <div class="fs-4 fw-semibold">{{ $stats['closed_leads'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-icon avatar-lg avatar-light-warning">
                                                            <span class="avatar-icon"><i
                                                                    data-feather="trending-down"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="text-muted fs-6">Total Cost</div>
                                                        <div class="fs-4 fw-semibold">
                                                            ₹{{ number_format($stats['total_cost'], 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-danger">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-icon avatar-lg avatar-light-danger">
                                                            <span class="avatar-icon"><i data-feather="clock"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="text-muted fs-6">Overdue Payments</div>
                                                        <div class="fs-4 fw-semibold">{{ $stats['overdue_payments'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Links -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0">Quick Reports</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <a href="{{ route('reports.leads') }}"
                                                            class="btn btn-outline-primary w-100">
                                                            <i data-feather="users" class="me-2"></i> Leads Report
                                                        </a>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <a href="{{ route('reports.revenue') }}"
                                                            class="btn btn-outline-success w-100">
                                                            <i data-feather="dollar-sign" class="me-2"></i> Revenue
                                                            Report
                                                        </a>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <a href="{{ route('reports.profit') }}"
                                                            class="btn btn-outline-info w-100">
                                                            <i data-feather="trending-up" class="me-2"></i> Profit
                                                            Report
                                                        </a>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <a href="{{ route('reports.export.leads') }}"
                                                            class="btn btn-outline-secondary w-100">
                                                            <i data-feather="download" class="me-2"></i> Export Leads
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Leads -->
                                <div class="row">
                                    <div class="col-md-8 mb-4">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between">
                                                <h5 class="mb-0">Recent Leads</h5>
                                                <a href="{{ route('leads.index') }}"
                                                    class="btn btn-sm btn-outline-primary">View All</a>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>TSQ</th>
                                                                <th>Customer</th>
                                                                <th>Service</th>
                                                                <th>Status</th>
                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($recentLeads as $lead)
                                                                <tr>
                                                                    <td><strong>{{ $lead->tsq }}</strong></td>
                                                                    <td>{{ $lead->customer_name }}</td>
                                                                    <td>{{ $lead->service?->name ?? 'N/A' }}</td>
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
                                                                                $statusColors[$lead->status] ??
                                                                                'bg-primary text-white';
                                                                        @endphp
                                                                        <span class="badge {{ $color }}">
                                                                            {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                                                        </span>
                                                                    </td>
                                                                    <td>{{ $lead->created_at->format('d M, Y') }}</td>
                                                                    <td><a href="{{ route('leads.show', $lead->id) }}"
                                                                            class="btn btn-sm btn-outline-primary">View</a>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="5" class="text-center">No recent leads
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Follow-up Reminders -->
                                    <div class="col-md-4 mb-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0">Follow-up Reminders</h5>
                                            </div>
                                            <div class="card-body">
                                                @forelse($followUps as $followUp)
                                                    <div class="card mb-2 border-left-primary">
                                                        <div class="card-body p-2">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <strong>{{ $followUp->lead->customer_name }}</strong>
                                                                    <small
                                                                        class="d-block text-muted">{{ $followUp->lead->tsq }}</small>
                                                                    @php $fuDate = $followUp->follow_up_at ?? $followUp->follow_up_date; @endphp
                                                                    <small
                                                                        class="text-danger">{{ $fuDate ? $fuDate->format('d M, Y') : '' }}</small>
                                                                </div>
                                                                <a href="{{ route('leads.show', $followUp->lead->id) }}"
                                                                    class="btn btn-sm btn-outline-primary">View</a>
                                                            </div>
                                                            <p class="mb-0 mt-1 small">
                                                                {{ Str::limit($followUp->remark, 50) }}</p>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-muted text-center">No follow-ups scheduled</p>
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
        @include('layouts.footer')
    </div>
@endsection
