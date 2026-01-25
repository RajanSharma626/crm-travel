@extends('layouts.app')

@section('title', 'Users List | Travel Shravel')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Users List</h1>
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                                    data-bs-target="#addUserModal">+ Add User</button>
                            </div>
                        </header>

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

                                <table class="table table-striped small table-bordered w-100 mb-5" id="usersTable">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Department</th>
                                            <th>Phone</th>
                                            <th>Date Created</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                            @if ($user->id != Auth::user()->id)
                                                <tr>
                                                    <td><strong>{{ $user->user_id ?? 'N/A' }}</strong></td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->role }}</td>
                                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                                    <td>{{ optional($user->created_at)->format('d M, Y') }}</td>
                                                    <td>
                                                        @if ($user->status == 'Active')
                                                            <span class="badge badge-soft-success">Active</span>
                                                        @elseif($user->status == 'Deactive')
                                                            <span class="badge badge-soft-danger">Deactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="d-flex">
                                                                @can('edit users')
                                                                    <a href="#"
                                                                        class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover edit-user-btn"
                                                                        data-user-id="{{ $user->id }}"
                                                                        data-bs-toggle="tooltip" data-placement="top"
                                                                        title="Edit User">
                                                                        <span class="icon">
                                                                            <span class="feather-icon">
                                                                                <i data-feather="edit"></i>
                                                                            </span>
                                                                        </span>
                                                                    </a>
                                                                @endcan
                                                                @can('delete users')
                                                                    <a class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover del-button"
                                                                        data-bs-toggle="tooltip" data-placement="top"
                                                                        title="Delete User"
                                                                        onclick="return confirm('Are you sure you want to delete {{ $user->name }}?')"
                                                                        href="{{ route('users.delete', $user->id) }}">
                                                                        <span class="icon">
                                                                            <span class="feather-icon">
                                                                                <i data-feather="trash"></i>
                                                                            </span>
                                                                        </span>
                                                                    </a>
                                                                @endcan
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">no records found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('users.store') }}" method="POST" id="addUserForm">
                        @csrf
                        <input type="hidden" name="id" id="editUserId" value="">
                        <input type="hidden" name="status" id="userStatus" value="Active">

                        <!-- Login Details Section -->
                        <div class="mb-4 border rounded-3 p-3 bg-light">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Login Details</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">User ID</label>
                                    <input type="text" name="user_id" id="user_id_input"
                                        placeholder="Auto-generated if left blank" class="form-control form-control-sm"
                                        value="{{ old('user_id') }}">
                                    <small class="text-muted">Leave blank to auto-generate</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group password-check">
                                        <span class="input-affix-wrapper">
                                            <input type="password" name="password" id="password_input"
                                                placeholder="Enter password" class="form-control form-control-sm"
                                                value="{{ old('password') }}" required>
                                            <a href="#" class="input-suffix text-muted">
                                                <span class="feather-icon"><i class="form-icon"
                                                        data-feather="eye"></i></span>
                                                <span class="feather-icon d-none"><i class="form-icon"
                                                        data-feather="eye-off"></i></span>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select name="role" id="role_input" class="form-select form-select-sm" required>
                                        <option value="">-- Select Department --</option>
                                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin
                                        </option>
                                        <option value="Sales Manager"
                                            {{ old('role') == 'Sales Manager' ? 'selected' : '' }}>Sales Manager</option>
                                        <option value="Sales" {{ old('role') == 'Sales' ? 'selected' : '' }}>Sales
                                        </option>
                                        <option value="Accounts Manager"
                                            {{ old('role') == 'Accounts Manager' ? 'selected' : '' }}>Accounts Manager
                                        </option>
                                        <option value="Accounts" {{ old('role') == 'Accounts' ? 'selected' : '' }}>
                                            Accounts</option>
                                        <option value="Operation Manager"
                                            {{ old('role') == 'Operation Manager' ? 'selected' : '' }}>Operation Manager
                                        </option>
                                        <option value="Operation" {{ old('role') == 'Operation' ? 'selected' : '' }}>
                                            Operation</option>
                                        <option value="Post Sales Manager"
                                            {{ old('role') == 'Post Sales Manager' ? 'selected' : '' }}>Post Sales Manager
                                        </option>
                                        <option value="Post Sales" {{ old('role') == 'Post Sales' ? 'selected' : '' }}>
                                            Post Sales</option>
                                        <option value="Delivery Manager"
                                            {{ old('role') == 'Delivery Manager' ? 'selected' : '' }}>Delivery Manager
                                        </option>
                                        <option value="Delivery" {{ old('role') == 'Delivery' ? 'selected' : '' }}>
                                            Delivery</option>
                                        <option value="HR" {{ old('role') == 'HR' ? 'selected' : '' }}>HR</option>
                                        <option value="Developer" {{ old('role') == 'Developer' ? 'selected' : '' }}>
                                            Developer</option>
                                    </select>
                                </div>
                                <div class="col-md-4" id="statusFieldContainer" style="display: none;">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status_input" class="form-select form-select-sm">
                                        <option value="Active">Active</option>
                                        <option value="Deactive">Deactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- User Details Section -->
                        <div class="mb-4 border rounded-3 p-3">
                            <h6 class="text-uppercase text-muted small fw-semibold mb-3">User Details</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Salutation</label>
                                    <select name="salutation" class="form-select form-select-sm">
                                        <option value="">-- Select --</option>
                                        <option value="Mr" {{ old('salutation') == 'Mr' ? 'selected' : '' }}>Mr
                                        </option>
                                        <option value="Mrs" {{ old('salutation') == 'Mrs' ? 'selected' : '' }}>Mrs
                                        </option>
                                        <option value="Miss" {{ old('salutation') == 'Miss' ? 'selected' : '' }}>Miss
                                        </option>
                                        <option value="Ms" {{ old('salutation') == 'Ms' ? 'selected' : '' }}>Ms
                                        </option>
                                        <option value="Dr" {{ old('salutation') == 'Dr' ? 'selected' : '' }}>Dr
                                        </option>
                                        <option value="Prof" {{ old('salutation') == 'Prof' ? 'selected' : '' }}>Prof
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" placeholder="e.g. John"
                                        class="form-control form-control-sm" value="{{ old('first_name') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" placeholder="e.g. Doe"
                                        class="form-control form-control-sm" value="{{ old('last_name') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control form-control-sm"
                                        value="{{ old('dob') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" placeholder="+91 98765 43210"
                                        class="form-control form-control-sm" value="{{ old('phone') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Email ID <span class="text-danger">*</span></label>
                                    <input type="email" name="email" placeholder="user@example.com"
                                        class="form-control form-control-sm" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address_line"
                                        placeholder="Street Address, Building, Apartment"
                                        class="form-control form-control-sm" value="{{ old('address_line') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" placeholder="City"
                                        class="form-control form-control-sm" value="{{ old('city') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" placeholder="State"
                                        class="form-control form-control-sm" value="{{ old('state') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Country</label>
                                    <select name="country" class="form-select form-select-sm">
                                        <option value="">-- Select Country --</option>
                                        <option value="Abu Dhabi" {{ old('country') == 'Abu Dhabi' ? 'selected' : '' }}>
                                            Abu Dhabi</option>
                                        <option value="America" {{ old('country') == 'America' ? 'selected' : '' }}>
                                            America</option>
                                        <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>
                                            Australia</option>
                                        <option value="Austria" {{ old('country') == 'Austria' ? 'selected' : '' }}>
                                            Austria</option>
                                        <option value="Azerbaijan" {{ old('country') == 'Azerbaijan' ? 'selected' : '' }}>
                                            Azerbaijan</option>
                                        <option value="Belgium" {{ old('country') == 'Belgium' ? 'selected' : '' }}>
                                            Belgium</option>
                                        <option value="Bhutan" {{ old('country') == 'Bhutan' ? 'selected' : '' }}>Bhutan
                                        </option>
                                        <option value="Cambodia" {{ old('country') == 'Cambodia' ? 'selected' : '' }}>
                                            Cambodia</option>
                                        <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada
                                        </option>
                                        <option value="Croatia" {{ old('country') == 'Croatia' ? 'selected' : '' }}>
                                            Croatia</option>
                                        <option value="Denmark" {{ old('country') == 'Denmark' ? 'selected' : '' }}>
                                            Denmark</option>
                                        <option value="Dubai" {{ old('country') == 'Dubai' ? 'selected' : '' }}>Dubai
                                        </option>
                                        <option value="Finland" {{ old('country') == 'Finland' ? 'selected' : '' }}>
                                            Finland</option>
                                        <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>France
                                        </option>
                                        <option value="Georgia" {{ old('country') == 'Georgia' ? 'selected' : '' }}>
                                            Georgia</option>
                                        <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>
                                            Germany</option>
                                        <option value="Greece" {{ old('country') == 'Greece' ? 'selected' : '' }}>Greece
                                        </option>
                                        <option value="Hong Kong" {{ old('country') == 'Hong Kong' ? 'selected' : '' }}>
                                            Hong Kong</option>
                                        <option value="Iceland" {{ old('country') == 'Iceland' ? 'selected' : '' }}>
                                            Iceland</option>
                                        <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India
                                        </option>
                                        <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>
                                            Indonesia</option>
                                        <option value="Ireland" {{ old('country') == 'Ireland' ? 'selected' : '' }}>
                                            Ireland</option>
                                        <option value="Italy" {{ old('country') == 'Italy' ? 'selected' : '' }}>Italy
                                        </option>
                                        <option value="Kazakhstan" {{ old('country') == 'Kazakhstan' ? 'selected' : '' }}>
                                            Kazakhstan</option>
                                        <option value="Laos" {{ old('country') == 'Laos' ? 'selected' : '' }}>Laos
                                        </option>
                                        <option value="Lithuania" {{ old('country') == 'Lithuania' ? 'selected' : '' }}>
                                            Lithuania</option>
                                        <option value="Luxembourg" {{ old('country') == 'Luxembourg' ? 'selected' : '' }}>
                                            Luxembourg</option>
                                        <option value="Macau" {{ old('country') == 'Macau' ? 'selected' : '' }}>Macau
                                        </option>
                                        <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>
                                            Malaysia</option>
                                        <option value="Mauritius" {{ old('country') == 'Mauritius' ? 'selected' : '' }}>
                                            Mauritius</option>
                                        <option value="Moldova" {{ old('country') == 'Moldova' ? 'selected' : '' }}>
                                            Moldova</option>
                                        <option value="Nepal" {{ old('country') == 'Nepal' ? 'selected' : '' }}>Nepal
                                        </option>
                                        <option value="Netherlands"
                                            {{ old('country') == 'Netherlands' ? 'selected' : '' }}>Netherlands</option>
                                        <option value="New Zealand"
                                            {{ old('country') == 'New Zealand' ? 'selected' : '' }}>New Zealand</option>
                                        <option value="Norway" {{ old('country') == 'Norway' ? 'selected' : '' }}>Norway
                                        </option>
                                        <option value="Phu Quoc" {{ old('country') == 'Phu Quoc' ? 'selected' : '' }}>Phu
                                            Quoc</option>
                                        <option value="Poland" {{ old('country') == 'Poland' ? 'selected' : '' }}>Poland
                                        </option>
                                        <option value="Portugal" {{ old('country') == 'Portugal' ? 'selected' : '' }}>
                                            Portugal</option>
                                        <option value="Russia" {{ old('country') == 'Russia' ? 'selected' : '' }}>Russia
                                        </option>
                                        <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>
                                            Singapore</option>
                                        <option value="Spain" {{ old('country') == 'Spain' ? 'selected' : '' }}>Spain
                                        </option>
                                        <option value="Srilanka" {{ old('country') == 'Srilanka' ? 'selected' : '' }}>
                                            Srilanka</option>
                                        <option value="Sweden" {{ old('country') == 'Sweden' ? 'selected' : '' }}>Sweden
                                        </option>
                                        <option value="Switzerland"
                                            {{ old('country') == 'Switzerland' ? 'selected' : '' }}>Switzerland</option>
                                        <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>
                                            Thailand</option>
                                        <option value="Turkey" {{ old('country') == 'Turkey' ? 'selected' : '' }}>Turkey
                                        </option>
                                        <option value="United Kingdom"
                                            {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom
                                        </option>
                                        <option value="Vatican City"
                                            {{ old('country') == 'Vatican City' ? 'selected' : '' }}>Vatican City</option>
                                        <option value="Vietnam" {{ old('country') == 'Vietnam' ? 'selected' : '' }}>
                                            Vietnam</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Pin Code</label>
                                    <input type="text" name="pin_code" placeholder="Pin Code"
                                        class="form-control form-control-sm" value="{{ old('pin_code') }}"
                                        maxlength="20">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="addUserSubmitBtn">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const addUserForm = document.getElementById('addUserForm');
                const addUserModalEl = document.getElementById('addUserModal');
                const usersBaseUrl = '{{ route('users') }}';

                // Initialize DataTable
                if ($.fn.DataTable) {
                    $('#usersTable').DataTable({
                        searching: false,
                        lengthChange: false,
                        info: false,
                        ordering: false,
                        paging: false,
                        drawCallback: function() {
                            if (typeof feather !== 'undefined') {
                                feather.replace();
                            }
                            // Reinitialize tooltips
                            if (typeof bootstrap !== 'undefined') {
                                const tooltipTriggerList = [].slice.call(document.querySelectorAll(
                                    '[data-bs-toggle="tooltip"]'));
                                tooltipTriggerList.map(function(tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl);
                                });
                            }
                        }
                    });
                }

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

                // Password visibility toggle (using jQuery like login page)
                // The password-check functionality is handled by init.js which is already loaded
                // No additional JavaScript needed as it uses event delegation

                // Handle form submission for edit mode
                addUserForm.addEventListener('submit', (event) => {
                    const editUserId = document.getElementById('editUserId');
                    const isEditMode = editUserId && editUserId.value;

                    if (isEditMode) {
                        // Update the form action for edit mode
                        addUserForm.action = '{{ route('users.update') }}';
                    }
                });

                // Reset form when modal is closed
                addUserModalEl.addEventListener('hidden.bs.modal', function() {
                    resetAddUserFormToAddMode();
                });

                // Function to populate form for edit
                const populateAddUserFormForEdit = (user) => {
                    if (!addUserForm) return;

                    const editUserId = document.getElementById('editUserId');
                    const modalLabel = document.getElementById('addUserModalLabel');
                    const submitBtn = document.getElementById('addUserSubmitBtn');

                    if (editUserId) editUserId.value = user.id;
                    if (modalLabel) modalLabel.textContent = 'Edit User';
                    if (submitBtn) submitBtn.textContent = 'Update User';

                    // Show status field for edit mode
                    const statusFieldContainer = document.getElementById('statusFieldContainer');
                    if (statusFieldContainer) {
                        statusFieldContainer.style.display = 'block';
                    }

                    // Update form action for direct form submission
                    addUserForm.action = '{{ route('users.update') }}';

                    // Populate form fields
                    if (addUserForm.elements['user_id']) addUserForm.elements['user_id'].value = user.user_id || '';
                    if (addUserForm.elements['role']) addUserForm.elements['role'].value = user.role || '';
                    if (addUserForm.elements['status']) addUserForm.elements['status'].value = user.status ||
                        'Active';
                    if (addUserForm.elements['salutation']) addUserForm.elements['salutation'].value = user
                        .salutation || '';
                    if (addUserForm.elements['first_name']) addUserForm.elements['first_name'].value = user
                        .first_name || '';
                    if (addUserForm.elements['last_name']) addUserForm.elements['last_name'].value = user
                        .last_name || '';
                    if (addUserForm.elements['dob']) {
                        const dobInput = addUserForm.elements['dob'];
                        dobInput.value = user.dob || '';
                    }
                    if (addUserForm.elements['phone']) addUserForm.elements['phone'].value = user.phone || '';
                    if (addUserForm.elements['email']) addUserForm.elements['email'].value = user.email || '';
                    if (addUserForm.elements['address_line']) addUserForm.elements['address_line'].value = user
                        .address_line || '';
                    if (addUserForm.elements['city']) addUserForm.elements['city'].value = user.city || '';
                    if (addUserForm.elements['state']) addUserForm.elements['state'].value = user.state || '';
                    if (addUserForm.elements['country']) addUserForm.elements['country'].value = user.country || '';
                    if (addUserForm.elements['pin_code']) addUserForm.elements['pin_code'].value = user.pin_code ||
                        '';

                    // Password field should be empty for edit
                    if (addUserForm.elements['password']) {
                        addUserForm.elements['password'].required = false;
                        addUserForm.elements['password'].value = '';
                        addUserForm.elements['password'].placeholder = 'Leave blank to keep current password';
                        addUserForm.elements['password'].setAttribute('type', 'password');
                    }

                    // Reset password toggle icon (using jQuery like login page)
                    if (typeof $ !== 'undefined') {
                        const passwordCheck = $('#password_input').closest('.password-check');
                        if (passwordCheck.length) {
                            passwordCheck.find('.input-suffix > span:first-child').removeClass('d-none');
                            passwordCheck.find('.input-suffix > span:last-child').addClass('d-none');
                        }
                    }
                };

                // Function to reset form to add mode
                const resetAddUserFormToAddMode = () => {
                    if (!addUserForm) return;

                    const editUserId = document.getElementById('editUserId');
                    const modalLabel = document.getElementById('addUserModalLabel');
                    const submitBtn = document.getElementById('addUserSubmitBtn');

                    if (editUserId) editUserId.value = '';
                    if (modalLabel) modalLabel.textContent = 'Add User';
                    if (submitBtn) submitBtn.textContent = 'Add User';

                    // Hide status field for add mode
                    const statusFieldContainer = document.getElementById('statusFieldContainer');
                    if (statusFieldContainer) {
                        statusFieldContainer.style.display = 'none';
                    }

                    // Reset form action
                    addUserForm.action = '{{ route('users.store') }}';

                    // Reset form
                    addUserForm.reset();

                    // Make password required again
                    if (addUserForm.elements['password']) {
                        addUserForm.elements['password'].required = true;
                        addUserForm.elements['password'].placeholder = 'Enter password';
                        addUserForm.elements['password'].setAttribute('type', 'password');
                    }

                    // Reset password toggle icon (using jQuery like login page)
                    if (typeof $ !== 'undefined') {
                        const passwordCheck = $('#password_input').closest('.password-check');
                        if (passwordCheck.length) {
                            passwordCheck.find('.input-suffix > span:first-child').removeClass('d-none');
                            passwordCheck.find('.input-suffix > span:last-child').addClass('d-none');
                        }
                    }
                };

                // Handle edit button clicks
                window.editUserClickHandler = function(event) {
                    if (event.target.closest('.edit-user-btn')) {
                        event.preventDefault();
                        const btn = event.target.closest('.edit-user-btn');
                        const userId = btn.getAttribute('data-user-id');

                        if (!userId) return;

                        // Fetch user data and open Add User modal in edit mode
                        fetch(`{{ url('/user/edit') }}/${userId}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.id) {
                                    // Open Add User modal
                                    if (addUserModalEl && typeof bootstrap !== 'undefined') {
                                        const modalInstance = bootstrap.Modal.getOrCreateInstance(
                                            addUserModalEl);
                                        populateAddUserFormForEdit(data);
                                        modalInstance.show();
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error loading user for edit:', error);
                                alert('Unable to load user details for editing.');
                            });
                    }
                };
                document.addEventListener('click', window.editUserClickHandler);
            });
        </script>
    @endpush
@endsection
