@extends('layouts.app')

@section('title', 'View Employee | Travel Shravel')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>View Employee</h1>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('hr.employees.edit', $employee->id) }}"
                                        class="btn btn-primary btn-sm">Edit</a>
                                    <a href="{{ route('hr.employees.index') }}"
                                        class="btn btn-outline-secondary btn-sm">Back to List</a>
                                </div>
                            </div>
                        </header>

                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                <div class="row g-4">
                                    {{-- Official Profile --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Official
                                                    Profile</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Employee ID</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->employee_id }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Salutation</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->salutation }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->name }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Date of Birth</label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            value="{{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('Y-m-d') : '' }}"
                                                            readonly>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Marital Status</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->marital_status }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Department</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->department }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Designation</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->designation }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Reporting Manager</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->reporting_manager }}" readonly>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Blood Group</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->blood_group }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Branch / Location</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->branch_location }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Date of Joining</label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            value="{{ $employee->doj ? \Carbon\Carbon::parse($employee->doj)->format('Y-m-d') : '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Date of Leaving</label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            value="{{ $employee->dol ? \Carbon\Carbon::parse($employee->dol)->format('Y-m-d') : '' }}"
                                                            readonly>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Employment Type</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->employment_type }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Employment Status</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->employment_status }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Starting Salary</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->starting_salary }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Last Withdrawn Salary</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->last_withdrawn_salary }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Login Details --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Login Details
                                                </h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">User ID</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->user_id }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Work E-mail</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->email }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Role</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->role ?? $employee->getRoleNameAttribute() }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        // Use optional helper or null coalescence to avoid errors
                                        $basicInfo = $employee->empBasicInfo ?? (object) [];
                                        $exitClearance = $employee->exitClearance ?? (object) [];
                                    @endphp

                                    {{-- Basic Information --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Basic
                                                    Information</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Previous Employer</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->previous_employer ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Contact Person</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->contact_person ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Contact Number</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->contact_number ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Reason for Leaving</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->reason_for_leaving ?? '' }}" readonly>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Highest Qualification</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->highest_qualification ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Specialization</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->specialization ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Year of Passing</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->year_of_passing ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Work Experience</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->work_experience ?? '' }}" readonly>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Father / Mother's Name</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->father_mother_name ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Father / Mother's Contact</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->father_mother_contact_number ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Nominee Name</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->nominee_name ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Nominee Contact</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->nominee_contact_number ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Emergency Contact</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->emergency_contact ?? '' }}" readonly>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Aadhar Number</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->aadhar_number ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">PAN Number</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->pan_number ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Passport Number</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->passport_number ?? '' }}" readonly>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Addresses --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Address</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Present Address</label>
                                                        <textarea rows="2" class="form-control form-control-sm" readonly>{{ $basicInfo->present_address ?? '' }}</textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Permanent Address</label>
                                                        <textarea rows="2" class="form-control form-control-sm" readonly>{{ $basicInfo->permanent_address ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Incentive & Performance --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Incentive &
                                                    Performance</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Incentive Eligibility</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->incentive_eligibility ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Incentive Type</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->incentive_type ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Monthly Target</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->monthly_target ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Incentive Payout Date</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->incentive_payout_date ? \Carbon\Carbon::parse($employee->incentive_payout_date)->format('Y-m-d') : '' }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Statutory & Payroll Details --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Statutory &
                                                    Payroll Details</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Bank Name</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->bank_name ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Account Number</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->account_number ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">IFSC Code</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->ifsc_code ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Salary Structure</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->salary_structure ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">PF Number</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->pf_number ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">ESIC Number</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->esic_number ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">UAN Number</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $employee->uan_number ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">PAN Card Number</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $basicInfo->pan_number ?? '' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Exit & Clearance --}}
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 text-uppercase text-muted small fw-semibold">Exit &
                                                    Clearance</h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Exit Initiated By</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $exitClearance->exit_initiated_by ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Resignation Date</label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            value="{{ !empty($exitClearance->resignation_date) ? \Carbon\Carbon::parse($exitClearance->resignation_date)->format('Y-m-d') : '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Notice Period</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $exitClearance->notice_period ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Last Working Day</label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            value="{{ !empty($exitClearance->last_working_day) ? \Carbon\Carbon::parse($exitClearance->last_working_day)->format('Y-m-d') : '' }}"
                                                            readonly>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Service Certificate Issued</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ isset($exitClearance->service_certificate_issued) ? ($exitClearance->service_certificate_issued ? 'Yes' : 'No') : '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Issuing Date</label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            value="{{ !empty($exitClearance->issuing_date) ? \Carbon\Carbon::parse($exitClearance->issuing_date)->format('Y-m-d') : '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Credit Card Handed Over?</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ $exitClearance->credit_card_handover ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Laptop Handed Over?</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ ($exitClearance->handed_over_laptop ?? '') == '1' ? 'Given' : $exitClearance->handed_over_laptop ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Mobile Handed Over?</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ ($exitClearance->handed_over_mobile ?? '') == '1' ? 'Given' : $exitClearance->handed_over_mobile ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">ID Card Handed Over?</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ ($exitClearance->handed_over_id_card ?? '') == '1' ? 'Given' : $exitClearance->handed_over_id_card ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">All Dues Cleared?</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ ($exitClearance->all_dues_cleared ?? '') == '1' ? 'Given' : $exitClearance->all_dues_cleared ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">Exit Interview Notes</label>
                                                        <textarea rows="3" class="form-control form-control-sm" readonly>{{ $exitClearance->exit_interview_notes ?? '' }}</textarea>
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
            </div>
        </div>
        @include('layouts.footer')
    </div>
@endsection
