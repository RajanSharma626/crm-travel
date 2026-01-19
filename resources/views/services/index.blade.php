@extends('layouts.app')
@section('title', 'Services | Travel Shreval')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <!-- Page Body -->
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
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

                                <!-- Add Service Form -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <form action="{{ route('services.store') }}" method="POST">
                                            @csrf
                                            <div class="row g-3 align-items-end">
                                                <div class="col-md-10">
                                                    <label for="service_name" class="form-label">Service Name</label>
                                                    <input type="text" class="form-control form-control-sm" 
                                                        id="service_name" name="name" 
                                                        value="{{ old('name') }}" 
                                                        placeholder="Enter service name" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                                        Add Service
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                @if (isset($services) && $services->count() > 0)
                                    <div class="text-muted small mb-2 px-3">
                                        Showing {{ $services->firstItem() ?? 0 }} out of {{ $services->total() }}
                                    </div>
                                @endif

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table id="ServiceTable"
                                        class="table table-striped table-bordered align-middle w-100 mb-3">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Service Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $sno = 1;
                                            @endphp
                                            @forelse ($services as $service)
                                                <tr id="service-row-{{ $service->id }}">
                                                    <td>{{ $sno }}</td>
                                                    <td>
                                                        <span class="service-name-display-{{ $service->id }}">{{ $service->name }}</span>
                                                        <form action="{{ route('services.update', $service->id) }}" method="POST" 
                                                            class="service-edit-form-{{ $service->id }} d-none">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" name="name" class="form-control" 
                                                                    value="{{ $service->name }}" required>
                                                                <button type="submit" class="btn btn-success btn-sm">
                                                                    <i class="bi bi-check"></i> Update
                                                                </button>
                                                                <button type="button" class="btn btn-secondary btn-sm cancel-edit" 
                                                                    data-service-id="{{ $service->id }}">
                                                                    <i class="bi bi-x"></i> Cancel
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-warning btn-sm edit-service-btn" 
                                                            data-service-id="{{ $service->id }}">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </button>
                                                        <form action="{{ route('services.destroy', $service->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                onclick="return confirm('Delete this service?')">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @php
                                                    $sno++
                                                @endphp
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">
                                                        No services found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $services->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('layouts.footer')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Edit button clicks
            document.querySelectorAll('.edit-service-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const serviceId = this.getAttribute('data-service-id');
                    const displaySpan = document.querySelector('.service-name-display-' + serviceId);
                    const editForm = document.querySelector('.service-edit-form-' + serviceId);
                    
                    // Hide display, show form
                    if (displaySpan) displaySpan.classList.add('d-none');
                    if (editForm) editForm.classList.remove('d-none');
                    
                    // Hide edit button, show cancel button is already in form
                    this.style.display = 'none';
                });
            });

            // Handle Cancel button clicks
            document.querySelectorAll('.cancel-edit').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const serviceId = this.getAttribute('data-service-id');
                    const displaySpan = document.querySelector('.service-name-display-' + serviceId);
                    const editForm = document.querySelector('.service-edit-form-' + serviceId);
                    const editBtn = document.querySelector('.edit-service-btn[data-service-id="' + serviceId + '"]');
                    
                    // Show display, hide form
                    if (displaySpan) displaySpan.classList.remove('d-none');
                    if (editForm) editForm.classList.add('d-none');
                    
                    // Show edit button
                    if (editBtn) editBtn.style.display = 'inline-block';
                });
            });

            // Handle form submission - show edit button again after successful update
            document.querySelectorAll('[class*="service-edit-form-"]').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    // The form will submit normally, and page will reload with success message
                    // The edit button will be visible again after reload
                });
            });
        });
    </script>
@endsection
