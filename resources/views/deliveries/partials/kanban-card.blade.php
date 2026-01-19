<div class="card mb-2 delivery-card" data-lead-id="{{ $lead->id }}" data-delivery-id="{{ $lead->delivery->id ?? '' }}" data-status="{{ $lead->delivery->delivery_status ?? 'Pending' }}" draggable="true">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <strong class="text-primary">{{ $lead->tsq }}</strong>
                <h6 class="mb-1 mt-1">
                    <a href="#" class="text-dark text-decoration-none view-lead-btn" data-lead-id="{{ $lead->id }}">
                        {{ $lead->customer_name }}
                    </a>
                </h6>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                    <i data-feather="more-vertical" style="width: 16px; height: 16px;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item view-lead-btn" href="#" data-lead-id="{{ $lead->id }}">
                            <i data-feather="eye" class="me-2" style="width: 14px; height: 14px;"></i> View Details
                        </a>
                    </li>
                    @can('assign deliveries')
                        <li>
                            <a class="dropdown-item assign-delivery-btn" href="#" data-lead-id="{{ $lead->id }}" data-delivery-id="{{ $lead->delivery->id ?? '' }}">
                                <i data-feather="user-plus" class="me-2" style="width: 14px; height: 14px;"></i> Assign
                            </a>
                        </li>
                    @endcan
                    @can('update deliveries')
                        <li>
                            <a class="dropdown-item update-delivery-status-btn" href="#" data-lead-id="{{ $lead->id }}" data-delivery-id="{{ $lead->delivery->id ?? '' }}">
                                <i data-feather="edit" class="me-2" style="width: 14px; height: 14px;"></i> Update Status
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        </div>
        
        <div class="small text-muted mb-2">
            <i data-feather="phone" style="width: 12px; height: 12px;"></i> {{ $lead->primary_phone ?? $lead->phone }}
        </div>
        
        @if ($lead->delivery)
            <div class="mb-2">
                @if ($lead->delivery->assignedTo)
                    <div class="small">
                        <i data-feather="user" style="width: 12px; height: 12px;"></i> 
                        <strong>Assigned:</strong> {{ $lead->delivery->assignedTo->name }}
                    </div>
                @else
                    <div class="small text-warning">
                        <i data-feather="alert-circle" style="width: 12px; height: 12px;"></i> Not Assigned
                    </div>
                @endif
            </div>
            
            @if ($lead->delivery->delivery_method)
                <div class="small mb-2">
                    <i data-feather="truck" style="width: 12px; height: 12px;"></i> 
                    <strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $lead->delivery->delivery_method)) }}
                </div>
            @endif
            
            @if ($lead->delivery->courier_id)
                <div class="small mb-2">
                    <i data-feather="package" style="width: 12px; height: 12px;"></i> 
                    <strong>Courier ID:</strong> {{ $lead->delivery->courier_id }}
                </div>
            @endif
            
            @if ($lead->delivery->delivered_at)
                <div class="small text-success">
                    <i data-feather="check-circle" style="width: 12px; height: 12px;"></i> 
                    Delivered: {{ $lead->delivery->delivered_at->format('d M, Y') }}
                </div>
            @endif
        @else
            <div class="small text-muted">
                <i data-feather="info" style="width: 12px; height: 12px;"></i> Delivery not created yet
            </div>
        @endif
        
        @if ($lead->latest_remark)
            <div class="mt-2 pt-2 border-top">
                <small class="text-muted">
                    <i data-feather="message-square" style="width: 12px; height: 12px;"></i>
                    {{ Str::limit($lead->latest_remark->remark, 40) }}
                </small>
            </div>
        @endif
    </div>
</div>

