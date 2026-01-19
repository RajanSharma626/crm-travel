@extends('layouts.app')

@section('title', 'Leads Information | Money Portal')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <!-- Page Body -->
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between">

                                <div class="d-flex justify-content-between w-100">
                                    <a class="contactapp-title link-dark">
                                        <h1>Lead Detail</h1>
                                    </a>
                                    <a href="{{ route('leads') }}"
                                        class="btn btn-sm btn-outline-warning flex-shrink-0 d-lg-inline-block d-none">
                                        Back</a>
                                </div>

                            </div>

                        </header>
                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                <div class="contact-list-view">
                                    <div class="card p-5">
                                        @if (session('success'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @if ($errors->any())
                                            <div class="alert alert-danger" role="alert">
                                                @foreach ($errors->all() as $error)
                                                    {{ $error . ',' }}
                                                @endforeach
                                            </div>
                                        @endif

                                        <form action="{{ route('lead.update.info') }}" method="post">
                                            @csrf

                                            <input type="number" name="id" id="" value="{{ $lead->id }}"
                                                hidden>
                                            <div class="modal-body">
                                                <h5 class="mb-5">Lead Information</h5>

                                                <div class="row gx-3">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">First Name*</label>
                                                            <input class="form-control" type="text" name="first_name"
                                                                value="{{ $lead->first_name }}" placeholder="First Name"
                                                                required />
                                                            @error('first_name')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Last Name</label>
                                                            <input class="form-control" type="text" name="last_name"
                                                                value="{{ $lead->last_name }}" placeholder="Last Name" />
                                                            @error('last_name')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Mobile*</label>
                                                            <input class="form-control" type="number"
                                                                value="{{ $lead->mobile }}" min="0" name="mobile"
                                                                placeholder="00000 00000" required />
                                                            @error('mobile')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Email Address*</label>
                                                            <input class="form-control" type="email" name="email"
                                                                value="{{ $lead->email }}" placeholder="Email Address"
                                                                required />
                                                            @error('email')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Lead Source</label>
                                                            <input class="form-control" type="text" name="lead_source"
                                                                value="{{ $lead->lead_source }}"
                                                                placeholder="Leade Source" />
                                                            @error('lead_source')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Keyword</label>
                                                            <input class="form-control" type="text" name="keyword"
                                                                value="{{ $lead->keyword }}" placeholder="Keyword" />
                                                            @error('keyword')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Loan Type*</label>
                                                            <select class="form-select" name="loan_type" required>
                                                                <option selected="">--</option>
                                                                <option value="Instant Loan"
                                                                    {{ $lead->loan_type == 'Instant Loan' ? 'Selected' : '' }}>
                                                                    Instant Loan</option>
                                                            </select>
                                                            @error('loan_type')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">City*</label>
                                                            <input type="text" class="form-control" name="city"
                                                                placeholder="Enter City" value="{{ $lead->city }}"
                                                                required>
                                                            @error('city')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Monthly Salary (INR)*</label>
                                                            <input class="form-control" type="number"
                                                                value="{{ $lead->monthly_salary }}" min="0"
                                                                name="monthly_salary" placeholder="₹0" required />
                                                            @error('monthly_salary')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Loan Amount (INR)*</label>
                                                            <input class="form-control" type="number" min="0"
                                                                name="loan_amount" placeholder="₹0"
                                                                value="{{ $lead->loan_amount }}" required />
                                                            @error('loan_amount')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Duration (In Days)*</label>
                                                            <input class="form-control" type="number"
                                                                value="{{ $lead->duration }}" min="0"
                                                                max="61" name="duration" placeholder="0 Days"
                                                                required oninput="if(this.value > 61) this.value = 61;" />
                                                            @error('duration')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Pancard Number*</label>
                                                            <input class="form-control" type="text"
                                                                value="{{ $lead->pancard_number }}" name="pancard_number"
                                                                placeholder="PAN No." required />
                                                            @error('pancard_number')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Gender*</label>
                                                            <select class="form-select" name="gender" required>
                                                                <option selected="">--</option>
                                                                <option value="Male"
                                                                    {{ $lead->gender == 'Male' ? 'Selected' : '' }}>Male
                                                                </option>
                                                                <option value="Female"
                                                                    {{ $lead->gender == 'Female' ? 'Selected' : '' }}>
                                                                    Female</option>
                                                                <option value="Other"
                                                                    {{ $lead->gender == 'Other' ? 'Selected' : '' }}>Other
                                                                </option>
                                                            </select>
                                                            @error('gender')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">DOB*</label>
                                                            <input class="form-control" type="date" name="dob"
                                                                value="{{ $lead->dob }}" required />
                                                            @error('dob')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Merital Status</label>
                                                            <select class="form-select" name="marital_status"
                                                                value="{{ old('marital_status') }}">
                                                                <option selected="">--</option>
                                                                <option value="Single"
                                                                    {{ $lead->marital_status == 'Single' ? 'Selected' : '' }}>
                                                                    Single</option>
                                                                <option value="Married"
                                                                    {{ $lead->marital_status == 'Married' ? 'Selected' : '' }}>
                                                                    Married</option>
                                                                <option value="Divorced"
                                                                    {{ $lead->marital_status == 'Devorced' ? 'Selected' : '' }}>
                                                                    Divorced</option>
                                                                <option value="Widowed"
                                                                    {{ $lead->marital_status == 'Widowed' ? 'Selected' : '' }}>
                                                                    Widowed</option>
                                                            </select>
                                                            @error('marital_status')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Education</label>
                                                            <input class="form-control" type="text" name="education"
                                                                value="{{ $lead->education }}" placeholder="Education" />
                                                            @error('education')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Disposition*</label>
                                                            <select class="form-select" name="disposition"
                                                                value="{{ old('disposition') }}" required>
                                                                <option value="" selected disabled>--</option>
                                                                <option value="Open">Open</option>
                                                                <option value="Closed">Closed</option>
                                                                <option value="Ringing">Ringing</option>
                                                                <option value="Busy">Busy</option>
                                                                <option value="Not reachable">Not reachable</option>
                                                                <option value="Wrong number">Wrong number</option>
                                                                <option value="Out of scope">Out of scope</option>
                                                                <option value="Call back">Call back</option>
                                                                <option value="Follow up">Follow up</option>
                                                                <option value="Rejected">Rejected</option>
                                                                <option value="Language barrier">Language barrier</option>
                                                                <option value="Nc Rejected">Nc Rejected</option>
                                                                <option value="Docs received">Docs received</option>
                                                                <option value="Approved">Approved</option>
                                                                <option value="Disbursed">Disbursed</option>
                                                                <option value="Reopen">Reopen</option>
                                                            </select>
                                                            @error('disposition')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Agent Name*</label>
                                                            <select class="form-select" name="agent_id"
                                                                value="{{ old('agent_id') }}">
                                                                <option value="" selected disabled>--</option>

                                                                @foreach ($emp as $users)
                                                                    <option value="{{ $users->id }}"
                                                                        {{ $users->id == Auth::id() ? 'selected' : '' }}>
                                                                        {{ $users->name }}
                                                                        ({{ $users->users_id }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('agent_id')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="form-label">Note*</label>
                                                            <textarea name="notes" class="form-control" required> {{ old('notes') }}</textarea>
                                                            @error('notes')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>


                                                </div>


                                            </div>
                                            <div class="modal-footer align-items-center">
                                                <button type="submit" class="btn btn-primary">Update
                                                    Lead</button>
                                            </div>
                                        </form>
                                    </div>



                                    {{-- ============================== Notes History ============================== --}}
                                    <div class="card p-5">
                                        <h5 class="mb-5">Notes History</h5>

                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Created Date</th>
                                                    <th>Updated By</th>
                                                    <th>Disposition</th>
                                                    <th>Note</th>
                                                    <th>Lead Assigned</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @if ($lead && is_iterable($lead->notesRelation))
                                                    @foreach ($lead->notesRelation as $note)
                                                        <tr>
                                                            <td>{{ $note->created_at->format('d M, Y h:i A') }}</td>
                                                            <td>{{ $note->user->name }} ({{ $note->user->users_id }})
                                                            </td>
                                                            <td>{{ $note->disposition }}</td>
                                                            <td>{{ $note->note }}</td>
                                                            <td>{{ $note->assignBy->name }}
                                                                ({{ $note->assignBy->users_id }})
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center">No Notes Found</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /Page Body -->
    </div>
@endsection
