@extends('layouts.app')
@section('title', 'Destinations | Travel Shreval')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <!-- Page Body -->
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Destinations</h1>
                                <a href="{{ route('destinations.create') }}" class="btn btn-primary btn-sm">
                                    + Add Destination
                                </a>
                            </div>
                        </header>

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

                                @if(isset($destinations) && $destinations->count() > 0)
                                <div class="text-muted small mb-2 px-3">
                                    Showing {{ $destinations->firstItem() ?? 0 }} out of {{ $destinations->total() }}
                                </div>
                                @endif

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table id="DestinationsTable"
                                        class="table table-striped table-bordered align-middle w-100 mb-3">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Country</th>
                                                <th>Destination Name</th>
                                                <th>Locations</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($destinations as $index => $destination)
                                                <tr>
                                                    <td>{{ ($destinations->currentPage() - 1) * $destinations->perPage() + $loop->iteration }}</td>
                                                    <td>{{ $destination->country ?? '-' }}</td>
                                                    <td>{{ $destination->name }}</td>
                                                    <td>
                                                        @if($destination->locations && $destination->locations->count() > 0)
                                                            @foreach($destination->locations as $location)
                                                                <span class="badge bg-primary me-1">{{ $location->name }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">No locations</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('destinations.edit', $destination->id) }}"
                                                            class="btn btn-outline-warning btn-sm">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </a>
                                                        <form action="{{ route('destinations.destroy', $destination->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                onclick="return confirm('Delete this destination?')">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">
                                                        No destinations found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $destinations->links('pagination::bootstrap-5') }}
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
@endsection
