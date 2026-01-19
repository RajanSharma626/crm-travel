@extends('layouts.app')

@section('title', 'Employee Details | Travel Shravel')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Employee Details</h1>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
                                </div>
                            </div>
                        </header>

                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                <form class="row g-4">
                                    {{-- Official Profile --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Official Profile</h6>
                                            </div>
                                            <div class="card-body py-3">
                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Employee ID</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->employee_id ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Salutation</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->salutation ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->name }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Date of Birth</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->date_of_birth ? $employee->date_of_birth->format('d M, Y') : ($employee->dob ? $employee->dob->format('d M, Y') : 'N/A') }}" readonly>
                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Marital Status</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->marital_status ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Department</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->department ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Designation</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->designation ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Reporting Manager</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->reporting_manager ?? 'N/A' }}" readonly>
                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Blood Group</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->blood_group ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Branch / Location</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->branch_location ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Date of Joining</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->doj ? $employee->doj->format('d M, Y') : ($employee->date_of_joining ?? 'N/A') }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Date of Leaving</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->dol ? $employee->dol->format('d M, Y') : ($employee->date_of_leaving ?? '— Active —') }}" readonly>
                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Employment Type</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->employment_type ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Employment Status</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->employment_status ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Starting Salary</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->starting_salary ? number_format($employee->starting_salary, 2) : 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Last Withdrawn Salary</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->last_withdrawn_salary ? number_format($employee->last_withdrawn_salary, 2) : 'N/A' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Login Details --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Login Details</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">User ID</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->user_id ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Work E-mail</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->login_work_email ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Password</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->password ? '••••••••' : 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">Role</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->role ?? 'N/A' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Basic Information --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Basic Information</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Previous Employer</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->previous_employer ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Contact Person</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->previous_employer_contact_person ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Contact Number</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->previous_employer_contact_number ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Reason for Leaving</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->reason_for_leaving ?? 'N/A' }}" readonly>
                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Highest Qualification</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->highest_qualification ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Specialization</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->specialization ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Year of Passing</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->year_of_passing ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Work Experience</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->work_experience ?? 'N/A' }}" readonly>
                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Father / Mother's Name</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->father_mother_name ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Father / Mother's Contact</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->father_mother_contact_number ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Nominee Name</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->nominee_name ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Nominee Contact</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->nominee_contact_number ?? 'N/A' }}" readonly>
                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Emergency Contact</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->emergency_contact ?? 'N/A' }}" readonly>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Aadhar Number</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->aadhar_number ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">PAN Number</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->pan_number ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Passport Number</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->passport_number ?? 'N/A' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Addresses --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Addresses</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Present Address</label>
                                                        <textarea rows="2" class="form-control form-control-sm" readonly>{{ $employee->present_address ?? 'N/A' }}</textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Permanent Address</label>
                                                        <textarea rows="2" class="form-control form-control-sm" readonly>{{ $employee->permanent_address ?? 'N/A' }}</textarea>
                                                        @if ($employee->permanent_same_as_present)
                                                            <small class="text-muted">Same as Present</small>
                                            @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Incentive & Performance --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Incentive & Performance</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Incentive Eligibility</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->incentive_eligibility ? 'Yes' : 'No' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Incentive Type</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->incentive_type ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Monthly Target</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->monthly_target ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Incentive Payout Date</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->incentive_payout_date ?? 'N/A' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Statutory & Payroll Details --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Statutory & Payroll Details</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Bank Name</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->bank_name ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Account Number</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->account_number ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">IFSC Code</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->ifsc_code ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Salary Structure</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->salary_structure ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">PF Number</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->pf_number ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">ESIC Number</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->esic_number ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">UAN Number</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->uan_number ?? 'N/A' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Exit & Clearance --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Exit & Clearance</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Exit Initiated By</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->exit_initiated_by ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Resignation Date</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->resignation_date ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Notice Period</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->notice_period ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Last Working Day</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->last_working_day ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Exit Interview Notes</label>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $employee->exit_interview_notes ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div class="row g-2">
                                                            <div class="col-md-3">
                                                                <label class="form-label">Service Certificate Issued</label>
                                                                <input type="text" class="form-control form-control-sm" value="{{ $employee->service_certificate_issued ? 'Yes' : 'No' }}" readonly>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Issuing Date</label>
                                                                <input type="text" class="form-control form-control-sm" value="{{ $employee->service_certificate_issue_date ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                                                <label class="form-label d-block">Assets & Dues</label>
                                                                <div class="d-flex flex-wrap gap-3">
                                                                    <span>Laptop: {{ $employee->handed_over_laptop ? 'Yes' : 'No' }}</span>
                                                                    <span>Mobile: {{ $employee->handed_over_mobile ? 'Yes' : 'No' }}</span>
                                                                    <span>ID Card: {{ $employee->handed_over_id_card ? 'Yes' : 'No' }}</span>
                                                                    <span>All Dues Cleared: {{ $employee->all_dues_cleared ? 'Yes' : 'No' }}</span>
                                        </div>
                                    </div>
                                </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('hr.employees.index') }}" class="btn btn-light border btn-sm">Back to List</a>
                                            <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-primary btn-sm">Edit Employee</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
@endsection
