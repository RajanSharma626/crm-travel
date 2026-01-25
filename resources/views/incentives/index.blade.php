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
                            </div>
                        </header>

                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (isset($incentives) && $incentives->count() > 0)
                                    <div class="text-muted small mb-2 px-3">
                                        Showing {{ $incentives->firstItem() ?? 0 }} out of {{ $incentives->total() }}
                                    </div>
                                @endif

                                <table class="table table-striped table-bordered w-100 mb-5" id="incentivesTable">
                                    <thead>
                                        <tr>
                                            <th>TSQ</th>
                                            <th>Customer</th>
                                            <th>Salesperson</th>
                                            <th>Profit Amount</th>
                                            <th>Incentive Amount</th>
                                            <th>Status</th>
                                            <th>Payout Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($incentives as $incentive)
                                            <tr>
                                                <td><strong>{{ $incentive->lead->tsq }}</strong></td>
                                                <td>{{ $incentive->lead->customer_name }}</td>
                                                <td>{{ $incentive->salesperson->name }}</td>
                                                <td>₹{{ number_format($incentive->profit_amount, 2) }}</td>
                                                <td><strong
                                                        class="text-success">₹{{ number_format($incentive->incentive_amount, 2) }}</strong>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $incentive->status == 'paid' ? 'success' : ($incentive->status == 'approved' ? 'primary' : 'warning') }}">
                                                        {{ ucfirst($incentive->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $incentive->payout_date ? $incentive->payout_date->format('d M, Y') : 'N/A' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('leads.show', $incentive->lead->id) }}"
                                                        class="btn btn-sm btn-outline-primary">View Lead</a>
                                                    @can('approve incentives')
                                                        @if ($incentive->status == 'pending')
                                                            <form action="{{ route('incentives.approve', $incentive->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-success">Approve</button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                                    @can('mark incentives paid')
                                                        @if ($incentive->status == 'approved')
                                                            <button class="btn btn-sm btn-primary mark-paid-btn"
                                                                data-incentive="{{ $incentive->id }}">Mark Paid</button>
                                                        @endif
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">no records found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-center">
                                    {{ $incentives->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    <!-- Mark Paid Modal -->
    <div class="modal fade" id="markPaidModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="markPaidForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Mark Incentive as Paid</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Payout Date</label>
                            <input type="date" name="payout_date" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Mark as Paid</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#incentivesTable').DataTable({
                scrollX: true,
                autoWidth: false,
            });

            $('.mark-paid-btn').on('click', function() {
                const incentiveId = $(this).data('incentive');
                $('#markPaidForm').attr('action', '/incentives/' + incentiveId + '/mark-paid');
                $('#markPaidModal').modal('show');
            });
        });
    </script>
@endsection
