@extends('layouts.app')
@section('title', 'Incentive Rules | Travel Shravel')
@section('content')
<div class="hk-pg-wrapper pb-0">
    <div class="hk-pg-body py-0">
        <div class="contactapp-wrap">
            <div class="contactapp-content">
                <div class="contactapp-detail-wrap">
                    <header class="contact-header">
                        <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                            <h1>Incentive Rules</h1>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRuleModal">+ Add Rule</button>
                        </div>
                    </header>

                    <div class="contact-body">
                        <div data-simplebar class="nicescroll-bar">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if(isset($rules) && $rules->count() > 0)
                            <div class="text-muted small mb-2 px-3">
                                Showing {{ $rules->firstItem() ?? 0 }} out of {{ $rules->total() }}
                            </div>
                            @endif

                            <table class="table table-striped table-bordered w-100 mb-5">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Rule Type</th>
                                        <th>Fixed %</th>
                                        <th>Fixed Amount</th>
                                        <th>Min Profit</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rules as $rule)
                                        <tr>
                                            <td><strong>{{ $rule->name }}</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $rule->rule_type)) }}</td>
                                            <td>{{ $rule->fixed_percentage ? $rule->fixed_percentage . '%' : 'N/A' }}</td>
                                            <td>{{ $rule->fixed_amount ? '₹' . number_format($rule->fixed_amount, 2) : 'N/A' }}</td>
                                            <td>₹{{ number_format($rule->min_profit_threshold, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $rule->active ? 'success' : 'secondary' }}">
                                                    {{ $rule->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-rule" data-rule='@json($rule)'>Edit</button>
                                                <form action="{{ route('incentive-rules.destroy', $rule->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this rule?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No incentive rules found</td></tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-center">
                                {{ $rules->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
</div>

<!-- Add/Edit Rule Modal -->
<div class="modal fade" id="addRuleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="ruleForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Incentive Rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rule Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rule Type</label>
                        <select name="rule_type" id="rule_type" class="form-select" required>
                            <option value="fixed_percentage">Fixed Percentage</option>
                            <option value="tiered_percentage">Tiered Percentage</option>
                            <option value="fixed_amount">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="mb-3" id="fixed_percentage_div">
                        <label class="form-label">Fixed Percentage (%)</label>
                        <input type="number" name="fixed_percentage" class="form-control" step="0.01" min="0" max="100">
                    </div>
                    <div class="mb-3" id="fixed_amount_div" style="display:none;">
                        <label class="form-label">Fixed Amount</label>
                        <input type="number" name="fixed_amount" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3" id="tiered_params_div" style="display:none;">
                        <label class="form-label">Tiered Parameters (JSON)</label>
                        <textarea name="params" class="form-control" rows="4" placeholder='[{"min": 0, "max": 10000, "percentage": 5}, {"min": 10000, "max": 50000, "percentage": 7}]'></textarea>
                        <small class="text-muted">Format: Array of objects with min, max, and percentage</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimum Profit Threshold</label>
                        <input type="number" name="min_profit_threshold" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" id="active" value="1" checked>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#ruleForm').attr('action', '{{ route("incentive-rules.store") }}');

        $('#rule_type').on('change', function() {
            const type = $(this).val();
            $('#fixed_percentage_div').toggle(type === 'fixed_percentage' || type === 'tiered_percentage');
            $('#fixed_amount_div').toggle(type === 'fixed_amount');
            $('#tiered_params_div').toggle(type === 'tiered_percentage');
        });

        $('.edit-rule').on('click', function() {
            const rule = $(this).data('rule');
            $('#addRuleModal .modal-title').text('Edit Incentive Rule');
            $('#ruleForm').attr('action', '/incentive-rules/' + rule.id);
            $('#ruleForm').append('<input type="hidden" name="_method" value="PUT">');
            $('#ruleForm input[name="name"]').val(rule.name);
            $('#ruleForm #rule_type').val(rule.rule_type).trigger('change');
            $('#ruleForm input[name="fixed_percentage"]').val(rule.fixed_percentage);
            $('#ruleForm input[name="fixed_amount"]').val(rule.fixed_amount);
            $('#ruleForm textarea[name="params"]').val(JSON.stringify(rule.params));
            $('#ruleForm input[name="min_profit_threshold"]').val(rule.min_profit_threshold);
            $('#ruleForm #active').prop('checked', rule.active);
            $('#addRuleModal').modal('show');
        });
    });
</script>
@endsection

