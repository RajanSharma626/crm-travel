@extends('layouts.app')
@section('title', 'Add Lead | Travel Shreval')

@php
    $leadDraftKey = 'lead_form_draft_' . auth()->id();
    $hasLeadOldInput = count(session()->getOldInput()) > 0;
@endphp

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <!-- Page Body -->
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">

                        <!-- Header -->
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Add Lead</h1>
                                <a href="{{ route('leads.index') }}" class="btn btn-outline-warning btn-sm">Back</a>
                            </div>
                        </header>

                        <!-- Body -->
                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                <!-- Alerts -->
                                @if (session('success'))
                                    <div class="alert alert-success mt-3">{{ session('success') }}</div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger mt-3">
                                        @foreach ($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="card border-0 shadow-sm mt-3">
                                    <div class="card-body p-4 p-lg-5">
                                        <form action="{{ route('leads.store') }}" method="POST" class="lead-form" id="leadCreateForm">
                                            @csrf

                                            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between mb-4">
                                                <div>
                                                    <h4 class="fw-semibold mb-1 text-primary">Create New Lead</h4>
                                                    <p class="text-muted mb-0">Capture enquiry details to kick-start the sales journey.</p>
                                                </div>
                                            </div>

                                            <p class="text-muted small mb-4">
                                                <span class="text-danger">*</span> Required fields
                                            </p>

                                            <!-- Customer Information -->
                                            <div class="border rounded-3 p-4 mb-4 bg-light">
                                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">Customer Information</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">Salutation</label>
                                                        <select name="salutation" class="form-select">
                                                            <option value="">-- Select --</option>
                                                            <option value="Mr" {{ old('salutation') == 'Mr' ? 'selected' : '' }}>Mr</option>
                                                            <option value="Mrs" {{ old('salutation') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                                            <option value="Ms" {{ old('salutation') == 'Ms' ? 'selected' : '' }}>Ms</option>
                                                            <option value="Dr" {{ old('salutation') == 'Dr' ? 'selected' : '' }}>Dr</option>
                                                            <option value="Prof" {{ old('salutation') == 'Prof' ? 'selected' : '' }}>Prof</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="first_name" class="form-control"
                                                            placeholder="e.g. Ramesh" value="{{ old('first_name') }}" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">Last Name</label>
                                                        <input type="text" name="last_name" class="form-control"
                                                            placeholder="e.g. Kumar" value="{{ old('last_name') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Contact Information -->
                                            <div class="border rounded-3 p-4 mb-4">
                                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">Contact Information</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Primary Number <span class="text-danger">*</span></label>
                                                        <input type="text" name="primary_phone" class="form-control"
                                                            placeholder="+91 98765 43210" maxlength="20"
                                                            value="{{ old('primary_phone') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Secondary Number</label>
                                                        <input type="text" name="secondary_phone" class="form-control"
                                                            placeholder="Alternate contact" maxlength="20"
                                                            value="{{ old('secondary_phone') }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Emergency No.</label>
                                                        <input type="text" name="other_phone" class="form-control"
                                                            placeholder="Emergency contact" maxlength="20"
                                                            value="{{ old('other_phone') }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                                        <input type="email" name="email" class="form-control"
                                                            placeholder="customer@email.com" value="{{ old('email') }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Address</label>
                                                        <input type="text" name="address" class="form-control"
                                                            placeholder="Street, City, Country" value="{{ old('address') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Travel Preferences -->
                                            <div class="border rounded-3 p-4 mb-4 bg-light">
                                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">Travel Preferences</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Service <span class="text-danger">*</span></label>
                                                        <select name="service_id" class="form-select" required>
                                                            <option value="">-- Select Service --</option>
                                                            @foreach ($services as $service)
                                                                <option value="{{ $service->id }}" {{ (string)old('service_id') === (string)$service->id ? 'selected' : '' }}>
                                                                    {{ $service->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Destination <span class="text-danger">*</span></label>
                                                        <select name="destination_id" class="form-select" required>
                                                            <option value="">-- Select Destination --</option>
                                                            @foreach ($destinations as $destination)
                                                                <option value="{{ $destination->id }}" {{ (string)old('destination_id') === (string)$destination->id ? 'selected' : '' }}>
                                                                    {{ $destination->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Travel Date</label>
                                                        <input type="date" name="travel_date" class="form-control" value="{{ old('travel_date') }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Adults <span class="text-danger">*</span></label>
                                                        <input type="number" name="adults" value="{{ old('adults', 1) }}" min="1"
                                                            class="form-control" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Children (2-5 yrs)</label>
                                                        <input type="number" name="children_2_5" value="{{ old('children_2_5', 0) }}" min="0"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Children (6-11 yrs)</label>
                                                        <input type="number" name="children_6_11" value="{{ old('children_6_11', 0) }}" min="0"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Infants (below 2 yrs)</label>
                                                        <input type="number" name="infants" value="{{ old('infants', 0) }}" min="0"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Assignment -->
                                            <div class="border rounded-3 p-4 mb-4">
                                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">Assignment</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Assign To</label>
                                                        <select name="assigned_user_id" class="form-select">
                                                            <option value="">-- Select Employee --</option>
                                                            @foreach ($employees as $employee)
                                                                @php
                                                                    $matchingUser = \App\Models\User::where('email', $employee->login_work_email)
                                                                        ->orWhere('email', $employee->user_id)
                                                                        ->first();
                                                                @endphp
                                                                <option value="{{ $employee->id }}"
                                                                    data-user-id="{{ $matchingUser->id ?? '' }}"
                                                                    {{ (string)old('assigned_user_id') === (string)$employee->id ? 'selected' : '' }}>
                                                                    {{ $employee->name }} @if($employee->user_id)({{ $employee->user_id }})@endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="new" {{ old('status', 'new') == 'new' ? 'selected' : '' }}>New</option>
                                                            <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                                            <option value="follow_up" {{ old('status') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                                                            <option value="priority" {{ old('status') == 'priority' ? 'selected' : '' }}>Priority</option>
                                                            <option value="booked" {{ old('status') == 'booked' ? 'selected' : '' }}>Booked</option>
                                                            <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                            <option value="refunded" {{ old('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-column flex-md-row justify-content-end gap-2 mt-4">
                                                <a href="{{ route('leads.index') }}" class="btn btn-light border">Cancel</a>
                                                <button type="submit" class="btn btn-primary px-4">Add Lead</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- /Body -->
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footer')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
                return;
            }

            const form = document.getElementById('leadCreateForm');
            if (!form) {
                return;
            }

            const draftStorageKey = '{{ $leadDraftKey }}';
            const hasOldInput = @json($hasLeadOldInput);
            const ignoredFields = ['_token', '_method'];

            const saveDraft = () => {
                const data = {};
                Array.from(form.elements).forEach((el) => {
                    if (!el.name || ignoredFields.includes(el.name) || el.disabled) {
                        return;
                    }
                    if ((el.type === 'checkbox' || el.type === 'radio')) {
                        if (el.checked) {
                            data[el.name] = el.value;
                        }
                        return;
                    }
                    data[el.name] = el.value;
                });
                localStorage.setItem(draftStorageKey, JSON.stringify(data));
            };

            const restoreDraft = () => {
                const stored = localStorage.getItem(draftStorageKey);
                if (!stored) {
                    return;
                }

                try {
                    const data = JSON.parse(stored);
                    Object.entries(data).forEach(([name, value]) => {
                        const node = form.elements[name];
                        if (!node) {
                            return;
                        }

                        if (node instanceof RadioNodeList) {
                            Array.from(node).forEach((input) => {
                                input.checked = input.value === value;
                            });
                        } else if (node.type === 'checkbox') {
                            node.checked = Boolean(value);
                        } else {
                            node.value = value;
                        }
                    });
                } catch (error) {
                    console.error('Failed to parse lead draft from storage', error);
                    localStorage.removeItem(draftStorageKey);
                }
            };

            const removeDraft = () => {
                localStorage.removeItem(draftStorageKey);
            };

            form.querySelectorAll('input, select, textarea').forEach((field) => {
                field.addEventListener('input', saveDraft);
                field.addEventListener('change', saveDraft);
            });

            form.addEventListener('submit', removeDraft);
            form.addEventListener('reset', removeDraft);

            if (!hasOldInput) {
                restoreDraft();
            }
        });
    </script>
@endsection
