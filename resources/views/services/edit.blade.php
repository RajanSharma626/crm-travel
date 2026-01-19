@extends('layouts.app')
@section('title', 'Edit Service | Travel Shravel')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Edit Service</h1>
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
                                    <form action="{{ route('services.update', $service->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row gx-3">
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label">Service Name*</label>
                                                <input type="text" name="name" class="form-control"
                                                    placeholder="Enter service name" value="{{ old('name', $service->name) }}" required>
                                            </div>

                                            <div class="col-sm-3 mb-3">
                                                <label class="form-label">Service Code</label>
                                                <input type="text" name="code" class="form-control"
                                                    placeholder="Enter service code" value="{{ old('code', $service->code) }}">
                                            </div>

                                            <div class="col-sm-3 mb-3">
                                                <label class="form-label">Default Price</label>
                                                <input type="number" name="default_price" class="form-control"
                                                    placeholder="₹0" step="0.01" value="{{ old('default_price', $service->default_price) }}">
                                            </div>

                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" {{ old('is_active', $service->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ old('is_active', $service->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" placeholder="Write short description...">{{ old('description', $service->description) }}</textarea>
                                            </div>
                                        </div>

                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-success">Update Service</button>
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

