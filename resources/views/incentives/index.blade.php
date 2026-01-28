@extends('layouts.app')
@section('title', 'Incentives | Travel Shravel')
@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Incentives</h1>
                                <div class="d-flex align-items-center">
                                    <form method="GET" action="{{ route('incentives.index') }}" class="d-flex gap-2 align-items-center">
                                        <select name="month" class="form-select form-select-sm">
                                            @php
                                                $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                                                $currentFull = now()->format('F');
                                                $selectedMonth = request()->has('month') ? request('month') : $currentFull;
                                            @endphp
                                            @foreach($months as $m)
                                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ $m }}</option>
                                            @endforeach
                                        </select>

                                        <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
                                        @if(request()->has('month'))
                                            <a href="{{ route('incentives.index') }}" class="btn btn-danger btn-sm">Clear</a>
                                        @endif
                                    </form>
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

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if (isset($incentives) && $incentives->count() > 0)
                                    <div class="text-muted small mb-2 px-3">
                                        Showing {{ $incentives->firstItem() ?? 0 }} out of {{ $incentives->total() }}
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered w-100 mb-5" id="incentivesTable">
                                        <thead>
                                            <tr>
                                                <th>Emp Code</th>
                                                <th>Department</th>
                                                <th>Month</th>
                                                <th>Target Files</th>
                                                <th>Achieved Target</th>
                                                <th>%age Achieved</th>
                                                <th>Incentive Payable</th>
                                                <th>Status</th>
                                                <th>Payout</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($salesUsers ?? [] as $user)
                                                <tr>
                                                    <td><strong>{{ $user->employee_id ?? $user->id ?? 'N/A' }}</strong></td>
                                                    <td>{{ $user->department ?? 'N/A' }}</td>
                                                    <td>
                                                        @php
                                                            try {
                                                                $displayMonth = request()->has('month') ? \Carbon\Carbon::parse(request('month'))->format('M') : now()->format('M');
                                                            } catch (\Exception $e) {
                                                                $displayMonth = now()->format('M');
                                                            }
                                                        @endphp
                                                        {{ $displayMonth }}
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No records found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination removed when listing sales users --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    {{-- Add Incentive modal removed; Add button hidden per request --}}

    <!-- Edit Incentive Modal -->
    <div class="modal fade" id="editIncentiveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="editIncentiveForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Incentive</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                                <input type="text" name="emp_code" id="edit_emp_code" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select name="department" id="edit_department" class="form-select" required>
                                    <option value="">Select Department</option>
                                    <option value="Sales">Sales</option>
                                    <option value="Post Sales">Post Sales</option>
                                    <option value="Operation">Operation</option>
                                    <option value="Ticketing">Ticketing</option>
                                    <option value="Visa">Visa</option>
                                    <option value="Insurance">Insurance</option>
                                    <option value="Accounts">Accounts</option>
                                    <option value="Delivery">Delivery</option>
                                    <option value="HR">HR</option>
                                    <option value="Customer Care">Customer Care</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Month <span class="text-danger">*</span></label>
                                <select name="month" id="edit_month" class="form-select" required>
                                    <option value="">Select Month</option>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Files <span class="text-danger">*</span></label>
                                <input type="number" name="target_files" id="edit_target_files" class="form-control"
                                    min="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Achieved Target <span class="text-danger">*</span></label>
                                <input type="number" name="achieved_target" id="edit_achieved_target"
                                    class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Percentage Achieved (%)</label>
                                <input type="number" name="percentage_achieved" id="edit_percentage_achieved"
                                    class="form-control" step="0.01" min="0" max="100" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Incentive Payable (₹) <span class="text-danger">*</span></label>
                                <input type="number" name="incentive_payable" id="edit_incentive_payable"
                                    class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payout Status <span class="text-danger">*</span></label>
                                <select name="payout_status" id="edit_payout_status" class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Incentive</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"> --}}

        <script>
            $(document).ready(function() {
                // DataTables disabled - using simple table with Laravel pagination
                // $('#incentivesTable').DataTable({
                //     scrollX: true,
                //     autoWidth: false,
                //     paging: false,
                //     info: false,
                //     searching: true,
                //     order: [
                //         [2, 'desc']
                //     ], // Sort by month by default
                // });



                // Auto-calculate percentage achieved on add form
                $('input[name="target_files"], input[name="achieved_target"]').on('input', function() {
                    const form = $(this).closest('form');
                    const targetFiles = parseFloat(form.find('input[name="target_files"]').val()) || 0;
                    const achievedTarget = parseFloat(form.find('input[name="achieved_target"]').val()) || 0;

                    if (targetFiles > 0) {
                        const percentage = (achievedTarget / targetFiles) * 100;
                        form.find('input[name="percentage_achieved"]').val(percentage.toFixed(2));
                    } else {
                        form.find('input[name="percentage_achieved"]').val('0.00');
                    }
                });

                // Edit button click handler
                $('.edit-incentive-btn').on('click', function() {
                    const id = $(this).data('id');
                    const empCode = $(this).data('emp-code');
                    const department = $(this).data('department');
                    const month = $(this).data('month');
                    const targetFiles = $(this).data('target-files');
                    const achievedTarget = $(this).data('achieved-target');
                    const percentageAchieved = $(this).data('percentage-achieved');
                    const incentivePayable = $(this).data('incentive-payable');
                    const status = $(this).data('status');
                    const payoutStatus = $(this).data('payout-status');

                    // Set form action
                    $('#editIncentiveForm').attr('action', '/incentives/' + id);

                    // Populate form fields
                    $('#edit_emp_code').val(empCode);
                    $('#edit_department').val(department);
                    $('#edit_month').val(month);
                    $('#edit_target_files').val(targetFiles);
                    $('#edit_achieved_target').val(achievedTarget);
                    $('#edit_percentage_achieved').val(percentageAchieved);
                    $('#edit_incentive_payable').val(incentivePayable);
                    $('#edit_status').val(status);
                    $('#edit_payout_status').val(payoutStatus);

                    // Show modal
                    $('#editIncentiveModal').modal('show');
                });

                // Initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });
        </script>
    @endpush
@endsection
