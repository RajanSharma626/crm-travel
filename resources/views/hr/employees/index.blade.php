@extends('layouts.app')

@section('title', 'Employees List | Travel Shravel')

@section('content')
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

                                <form method="GET" action="{{ route('hr.employees.index') }}" class="row g-3 mb-4">
                                    <div class="col-md-4 col-lg-3">
                                        <label for="search" class="form-label">Search</label>
                                        <input type="text" name="search" id="search" class="form-control form-control-sm"
                                            placeholder="Search by name, ID, email, department..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="department" class="form-label">Department</label>
                                        <select name="department" id="department" class="form-select form-select-sm">
                                            <option value="">-- All Departments --</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept }}" @selected(request('department') === $dept)>{{ $dept }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select form-select-sm">
                                            <option value="">-- All --</option>
                                            <option value="active" @selected(request('status') === 'active')>Active</option>
                                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-lg-2 align-self-end">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="ri-search-line me-1"></i> Filter
                                        </button>
                                    </div>
                                    <div class="col-md-2 col-lg-2 align-self-end">
                                        <a href="{{ route('hr.employees.create') }}" class="btn btn-primary btn-sm w-100">+ Add Employee</a>
                                    </div>
                                    @if (request('search') || request('department') || request('status'))
                                        <div class="col-md-2 col-lg-2 align-self-end">
                                            <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-danger btn-sm w-100">
                                                Clear Filters
                                            </a>
                                        </div>
                                    @endif
                                </form>

                                @if(isset($employees) && $employees->count() > 0)
                                <div class="text-muted small mb-2 px-3">
                                    Showing {{ $employees->firstItem() ?? 0 }} out of {{ $employees->total() }}
                                </div>
                                @endif

                                <table class="table table-striped small table-bordered w-100 mb-5">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>DOJ</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($employees as $employee)
                                            <tr>
                                                <td><strong>{{ $employee->employee_id ?? 'N/A' }}</strong></td>
                                                <td>{{ $employee->name }}</td>
                                                <td>{{ $employee->work_email ?? 'N/A' }}</td>
                                                <td>{{ $employee->department ?? 'N/A' }}</td>
                                                <td>{{ $employee->designation ?? 'N/A' }}</td>
                                                <td>{{ $employee->doj ? $employee->doj->format('d M, Y') : 'N/A' }}</td>
                                                <td>
                                                    @if ($employee->dol)
                                                        <span class="badge badge-soft-danger">Inactive</span>
                                                    @else
                                                        <span class="badge badge-soft-success">Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex">
                                                            <a href="{{ route('hr.employees.show', $employee) }}"
                                                                class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover"
                                                                data-bs-toggle="tooltip" data-placement="top" title="View Employee">
                                                                <span class="icon">
                                                                    <span class="feather-icon">
                                                                        <i data-feather="eye"></i>
                                                                    </span>
                                                                </span>
                                                            </a>
                                                            <a href="{{ route('hr.employees.edit', $employee) }}"
                                                                class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover"
                                                                data-bs-toggle="tooltip" data-placement="top" title="Edit Employee">
                                                                <span class="icon">
                                                                    <span class="feather-icon">
                                                                        <i data-feather="edit"></i>
                                                                    </span>
                                                                </span>
                                                            </a>
                                                            <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST"
                                                                class="d-inline" onsubmit="return confirm('Are you sure you want to delete {{ $employee->name }}?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover"
                                                                    data-bs-toggle="tooltip" data-placement="top" title="Delete Employee">
                                                                    <span class="icon">
                                                                        <span class="feather-icon">
                                                                            <i data-feather="trash"></i>
                                                                        </span>
                                                                    </span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No employees found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mb-3 px-3">
                                    {{ $employees->links('pagination::bootstrap-5') }}
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Initialize Bootstrap tooltips
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
    </script>
@endpush

