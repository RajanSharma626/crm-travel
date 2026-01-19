@extends('layouts.app')
@section('title', 'Add Service | Travel Shreval')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Add Service</h1>
                                <a href="{{ route('services.index') }}" class="btn btn-outline-warning btn-sm">Back</a>
                            </div>
                        </header>

                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
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

                                <div class="card p-5 mt-3">
                                    <form action="{{ route('services.store') }}" method="POST">
                                        @csrf
                                        <div class="row gx-3">
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label">Service Name*</label>
                                                <input type="text" name="name" class="form-control"
                                                    placeholder="Enter service name" required>
                                            </div>

                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" selected>Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" placeholder="Write short description..."></textarea>
                                            </div>
                                        </div>

                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-success">Save Service</button>
                                            <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </form>
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
