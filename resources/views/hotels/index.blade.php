@extends('layouts.app')
@section('title', 'Hotels | Travel Shravel')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <!-- Page Body -->
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Hotels</h1>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addHotelModal">
                                    <i data-feather="plus" style="width: 14px; height: 14px;"></i> Add Hotel
                                </button>
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

                                <!-- Filters -->
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body">
                                        <form method="GET" action="{{ route('hotels.index') }}" id="filterForm">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small">Search by Name</label>
                                                    <input type="text" name="search"
                                                        class="form-control form-control-sm" placeholder="Hotel name..."
                                                        value="{{ request('search') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Country</label>
                                                    <select name="country" id="filterCountry"
                                                        class="form-select form-select-sm">
                                                        <option value="">All Countries</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country }}"
                                                                {{ request('country') == $country ? 'selected' : '' }}>
                                                                {{ $country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Destination</label>
                                                    <select name="destination_id" id="filterDestination"
                                                        class="form-select form-select-sm">
                                                        <option value="">All Destinations</option>
                                                        @foreach ($destinations as $destination)
                                                            <option value="{{ $destination->id }}"
                                                                {{ request('destination_id') == $destination->id ? 'selected' : '' }}>
                                                                {{ $destination->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Location</label>
                                                    <select name="location_id" id="filterLocation"
                                                        class="form-select form-select-sm">
                                                        <option value="">All Locations</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i data-feather="filter" style="width: 14px; height: 14px;"></i>
                                                        Apply Filters
                                                    </button>
                                                    <a href="{{ route('hotels.index') }}"
                                                        class="btn btn-outline-secondary btn-sm">
                                                        <i data-feather="x" style="width: 14px; height: 14px;"></i> Clear
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                @if (isset($hotels) && $hotels->count() > 0)
                                    <div class="text-muted small mb-3 px-3">
                                        Showing {{ $hotels->firstItem() ?? 0 }} - {{ $hotels->lastItem() ?? 0 }} of
                                        {{ $hotels->total() }} hotels
                                    </div>
                                @endif

                                <!-- Hotels Grid -->
                                <div class="row g-4 mb-4">
                                    @forelse ($hotels as $hotel)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-sm border-0 hover-shadow transition">
                                                <div class="card-body">
                                                    <h5 class="card-title mb-3">{{ $hotel->name }}</h5>

                                                    @if ($hotel->destination || $hotel->location)
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i data-feather="map-pin"
                                                                    style="width: 12px; height: 12px;"></i>
                                                                @if ($hotel->destination)
                                                                    {{ $hotel->destination->name }}
                                                                @endif
                                                                @if ($hotel->location)
                                                                    @if ($hotel->destination)
                                                                        ,
                                                                    @endif
                                                                    {{ $hotel->location->name }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    @endif

                                                    @if ($hotel->country)
                                                        <div class="mb-2">
                                                            <span class="badge bg-light text-dark">
                                                                <i data-feather="globe"
                                                                    style="width: 12px; height: 12px;"></i>
                                                                {{ $hotel->country }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if ($hotel->contact_no_1)
                                                        <div class="mb-1">
                                                            <small class="text-muted">
                                                                <i data-feather="phone"
                                                                    style="width: 12px; height: 12px;"></i>
                                                                {{ $hotel->contact_no_1 }}
                                                            </small>
                                                        </div>
                                                    @endif

                                                    @if ($hotel->contact_no_2)
                                                        <div class="mb-1">
                                                            <small class="text-muted">
                                                                <i data-feather="phone"
                                                                    style="width: 12px; height: 12px;"></i>
                                                                {{ $hotel->contact_no_2 }}
                                                            </small>
                                                        </div>
                                                    @endif

                                                    @if ($hotel->contact_person_name || $hotel->contact_person_phone)
                                                        <div class="mt-2 mb-1">
                                                            <small class="text-muted">
                                                                <i data-feather="user"
                                                                    style="width: 12px; height: 12px;"></i>
                                                                @if ($hotel->contact_person_name)
                                                                    {{ $hotel->contact_person_name }}
                                                                @endif
                                                                @if ($hotel->contact_person_phone)
                                                                    @if ($hotel->contact_person_name)
                                                                        -
                                                                    @endif
                                                                    {{ $hotel->contact_person_phone }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    @endif

                                                    @if ($hotel->address)
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                <i data-feather="map"
                                                                    style="width: 12px; height: 12px;"></i>
                                                                {{ Str::limit($hotel->address, 60) }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="card-footer bg-white border-top-0">
                                                    <div class="d-flex gap-2">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-warning flex-fill edit-hotel-btn"
                                                            data-hotel-id="{{ $hotel->id }}">
                                                            <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                            Edit
                                                        </button>
                                                        <form action="{{ route('hotels.destroy', $hotel->id) }}"
                                                            method="POST" class="d-inline flex-fill"
                                                            onsubmit="return confirm('Are you sure you want to delete this hotel?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-danger w-100">
                                                                <i data-feather="trash-2"
                                                                    style="width: 14px; height: 14px;"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body text-center py-5">
                                                    <i data-feather="inbox"
                                                        style="width: 48px; height: 48px; color: #6c757d;"></i>
                                                    <p class="text-muted mt-3 mb-0">no records found</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Pagination -->
                                @if ($hotels->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $hotels->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('layouts.footer')
    </div>

    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .transition {
            transition: all 0.3s ease;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Cascading filters: Country → Destination → Location
            const countrySelect = document.getElementById('filterCountry');
            const destinationSelect = document.getElementById('filterDestination');
            const locationSelect = document.getElementById('filterLocation');
            const selectedDestinationId = '{{ request('destination_id') }}';
            const selectedLocationId = '{{ request('location_id') }}';

            // Handle country change to load destinations
            if (countrySelect) {
                countrySelect.addEventListener('change', function() {
                    const country = this.value;
                    destinationSelect.innerHTML = '<option value="">All Destinations</option>';
                    locationSelect.innerHTML = '<option value="">All Locations</option>';

                    if (country) {
                        fetch(`/api/hotels/destinations/${encodeURIComponent(country)}`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(destination => {
                                    const option = document.createElement('option');
                                    option.value = destination.id;
                                    option.textContent = destination.name;
                                    destinationSelect.appendChild(option);
                                });
                            })
                            .catch(error => console.error('Error loading destinations:', error));
                    }
                });

                // Load destinations on page load if country is selected
                if (countrySelect.value) {
                    const country = countrySelect.value;
                    fetch(`/api/hotels/destinations/${encodeURIComponent(country)}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(destination => {
                                const option = document.createElement('option');
                                option.value = destination.id;
                                option.textContent = destination.name;
                                if (destination.id == selectedDestinationId) {
                                    option.selected = true;
                                }
                                destinationSelect.appendChild(option);
                            });

                            // Trigger destination change if one is selected
                            if (selectedDestinationId && destinationSelect.value) {
                                destinationSelect.dispatchEvent(new Event('change'));
                            }
                        })
                        .catch(error => console.error('Error loading destinations:', error));
                }
            }

            // Handle destination change to load locations
            if (destinationSelect) {
                destinationSelect.addEventListener('change', function() {
                    const destinationId = this.value;
                    locationSelect.innerHTML = '<option value="">All Locations</option>';

                    if (destinationId) {
                        fetch(`/api/destinations/${destinationId}/locations`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(location => {
                                    const option = document.createElement('option');
                                    option.value = location.id;
                                    option.textContent = location.name;
                                    if (location.id == selectedLocationId) {
                                        option.selected = true;
                                    }
                                    locationSelect.appendChild(option);
                                });
                            })
                            .catch(error => console.error('Error loading locations:', error));
                    }
                });

                // Load locations on page load if destination is selected (and no country filter)
                if (!countrySelect.value && destinationSelect.value) {
                    const destinationId = destinationSelect.value;
                    fetch(`/api/destinations/${destinationId}/locations`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(location => {
                                const option = document.createElement('option');
                                option.value = location.id;
                                option.textContent = location.name;
                                if (location.id == selectedLocationId) {
                                    option.selected = true;
                                }
                                locationSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error loading locations:', error));
                }
            }

            // Modal handling for Add Hotel
            const addHotelModal = document.getElementById('addHotelModal');
            const addCountrySelect = document.getElementById('add_country');
            const addDestinationSelect = document.getElementById('add_destination_id');
            const addLocationSelect = document.getElementById('add_location_id');

            if (addCountrySelect) {
                addCountrySelect.addEventListener('change', function() {
                    const country = this.value;
                    addDestinationSelect.innerHTML = '<option value="">-- Select Destination --</option>';
                    addLocationSelect.innerHTML = '<option value="">-- Select Location --</option>';

                    if (country) {
                        console.log('Fetching destinations for country:', country);
                        fetch(`/api/hotels/destinations/${encodeURIComponent(country)}`)
                            .then(response => {
                                console.log('Response status:', response.status);
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Destinations received:', data);
                                if (Array.isArray(data) && data.length > 0) {
                                    data.forEach(destination => {
                                        const option = document.createElement('option');
                                        option.value = destination.id;
                                        option.textContent = destination.name;
                                        addDestinationSelect.appendChild(option);
                                    });
                                } else {
                                    console.log('No destinations found for this country');
                                }
                            })
                            .catch(error => {
                                console.error('Error loading destinations:', error);
                                alert(
                                    'Error loading destinations. Please check the console for details.'
                                );
                            });
                    }
                });

                addDestinationSelect.addEventListener('change', function() {
                    const destinationId = this.value;
                    addLocationSelect.innerHTML = '<option value="">-- Select Location --</option>';

                    if (destinationId) {
                        fetch(`/api/destinations/${destinationId}/locations`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(location => {
                                    const option = document.createElement('option');
                                    option.value = location.id;
                                    option.textContent = location.name;
                                    addLocationSelect.appendChild(option);
                                });
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            }

            // Edit Hotel Modal
            document.querySelectorAll('.edit-hotel-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const hotelId = this.getAttribute('data-hotel-id');
                    fetch(`/hotels/${hotelId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(hotel => {
                            document.getElementById('edit_hotel_id').value = hotel.id;
                            document.getElementById('edit_name').value = hotel.name;
                            document.getElementById('edit_contact_no_1').value = hotel
                                .contact_no_1 || '';
                            document.getElementById('edit_contact_no_2').value = hotel
                                .contact_no_2 || '';
                            document.getElementById('edit_contact_person_name').value = hotel
                                .contact_person_name || '';
                            document.getElementById('edit_contact_person_phone').value = hotel
                                .contact_person_phone || '';
                            document.getElementById('edit_address').value = hotel.address || '';

                            // Set country and load destinations
                            const editCountrySelect = document.getElementById('edit_country');
                            editCountrySelect.value = hotel.country || '';

                            if (hotel.country) {
                                fetch(
                                        `/api/hotels/destinations/${encodeURIComponent(hotel.country)}`
                                    )
                                    .then(response => response.json())
                                    .then(data => {
                                        const editDestSelect = document.getElementById(
                                            'edit_destination_id');
                                        editDestSelect.innerHTML =
                                            '<option value="">-- Select Destination --</option>';
                                        data.forEach(destination => {
                                            const option = document.createElement(
                                                'option');
                                            option.value = destination.id;
                                            option.textContent = destination.name;
                                            if (destination.id == hotel
                                                .destination_id) {
                                                option.selected = true;
                                            }
                                            editDestSelect.appendChild(option);
                                        });

                                        // Load locations if destination is set
                                        if (hotel.destination_id) {
                                            fetch(
                                                    `/api/destinations/${hotel.destination_id}/locations`
                                                )
                                                .then(response => response.json())
                                                .then(data => {
                                                    const editLocSelect = document
                                                        .getElementById(
                                                            'edit_location_id');
                                                    editLocSelect.innerHTML =
                                                        '<option value="">-- Select Location --</option>';
                                                    data.forEach(location => {
                                                        const option = document
                                                            .createElement(
                                                                'option');
                                                        option.value = location
                                                            .id;
                                                        option.textContent =
                                                            location.name;
                                                        if (location.id == hotel
                                                            .location_id) {
                                                            option.selected =
                                                                true;
                                                        }
                                                        editLocSelect
                                                            .appendChild(
                                                                option);
                                                    });
                                                });
                                        }
                                    });
                            }

                            const editModal = new bootstrap.Modal(document.getElementById(
                                'editHotelModal'));
                            editModal.show();
                            feather.replace();
                        })
                        .catch(error => console.error('Error:', error));
                });
            });

            // Edit modal cascading dropdowns
            const editCountrySelect = document.getElementById('edit_country');
            const editDestinationSelect = document.getElementById('edit_destination_id');
            const editLocationSelect = document.getElementById('edit_location_id');

            if (editCountrySelect) {
                editCountrySelect.addEventListener('change', function() {
                    const country = this.value;
                    editDestinationSelect.innerHTML = '<option value="">-- Select Destination --</option>';
                    editLocationSelect.innerHTML = '<option value="">-- Select Location --</option>';

                    if (country) {
                        fetch(`/api/hotels/destinations/${encodeURIComponent(country)}`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(destination => {
                                    const option = document.createElement('option');
                                    option.value = destination.id;
                                    option.textContent = destination.name;
                                    editDestinationSelect.appendChild(option);
                                });
                            });
                    }
                });

                editDestinationSelect.addEventListener('change', function() {
                    const destinationId = this.value;
                    editLocationSelect.innerHTML = '<option value="">-- Select Location --</option>';

                    if (destinationId) {
                        fetch(`/api/destinations/${destinationId}/locations`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(location => {
                                    const option = document.createElement('option');
                                    option.value = location.id;
                                    option.textContent = location.name;
                                    editLocationSelect.appendChild(option);
                                });
                            });
                    }
                });
            }

            // Handle edit form submission
            const editHotelForm = document.getElementById('editHotelForm');
            if (editHotelForm) {
                editHotelForm.addEventListener('submit', function(e) {
                    const hotelId = document.getElementById('edit_hotel_id').value;
                    this.action = `/hotels/${hotelId}`;
                });
            }
        });
    </script>

    <!-- Add Hotel Modal -->
    <div class="modal fade" id="addHotelModal" tabindex="-1" aria-labelledby="addHotelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('hotels.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addHotelModalLabel">
                            <i data-feather="plus-circle" style="width: 18px; height: 18px;"></i> Add New Hotel
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Row 1: Country, Destination, Location -->
                            <div class="col-md-4">
                                <label class="form-label small">Country</label>
                                <select name="country" id="add_country" class="form-select form-select-sm">
                                    <option value="">-- Select Country --</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Destination</label>
                                <select name="destination_id" id="add_destination_id" class="form-select form-select-sm">
                                    <option value="">-- Select Destination --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Location</label>
                                <select name="location_id" id="add_location_id" class="form-select form-select-sm">
                                    <option value="">-- Select Location --</option>
                                </select>
                            </div>

                            <!-- Row 2: Hotel Name, Contact No. 1, Contact No. 2 -->
                            <div class="col-md-4">
                                <label class="form-label small">Hotel Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Contact No. 1</label>
                                <input type="text" name="contact_no_1" class="form-control form-control-sm"
                                    placeholder="+91 1234567890">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Contact No. 2</label>
                                <input type="text" name="contact_no_2" class="form-control form-control-sm"
                                    placeholder="+91 0987654321">
                            </div>

                            <!-- Row 3: Contact Person Name, Contact Person Phone, Address -->
                            <div class="col-md-4">
                                <label class="form-label small">Contact Person Name</label>
                                <input type="text" name="contact_person_name" class="form-control form-control-sm"
                                    placeholder="Contact person name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Contact Person Phone</label>
                                <input type="text" name="contact_person_phone" class="form-control form-control-sm"
                                    placeholder="+91 0987654321">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Address</label>
                                <textarea name="address" class="form-control form-control-sm" rows="1"
                                    placeholder="Full address of the hotel"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i data-feather="save" style="width: 14px; height: 14px;"></i> Save Hotel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Hotel Modal -->
    <div class="modal fade" id="editHotelModal" tabindex="-1" aria-labelledby="editHotelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editHotelForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_hotel_id" name="hotel_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editHotelModalLabel">
                            <i data-feather="edit" style="width: 18px; height: 18px;"></i> Edit Hotel
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Row 1: Country, Destination, Location -->
                            <div class="col-md-4">
                                <label class="form-label small">Country</label>
                                <select name="country" id="edit_country" class="form-select form-select-sm">
                                    <option value="">-- Select Country --</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Destination</label>
                                <select name="destination_id" id="edit_destination_id"
                                    class="form-select form-select-sm">
                                    <option value="">-- Select Destination --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Location</label>
                                <select name="location_id" id="edit_location_id" class="form-select form-select-sm">
                                    <option value="">-- Select Location --</option>
                                </select>
                            </div>

                            <!-- Row 2: Hotel Name, Contact No. 1, Contact No. 2 -->
                            <div class="col-md-4">
                                <label class="form-label small">Hotel Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_name" class="form-control form-control-sm"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Contact No. 1</label>
                                <input type="text" name="contact_no_1" id="edit_contact_no_1"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Contact No. 2</label>
                                <input type="text" name="contact_no_2" id="edit_contact_no_2"
                                    class="form-control form-control-sm">
                            </div>

                            <!-- Row 3: Contact Person Name, Contact Person Phone, Address -->
                            <div class="col-md-4">
                                <label class="form-label small">Contact Person Name</label>
                                <input type="text" name="contact_person_name" id="edit_contact_person_name"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Contact Person Phone</label>
                                <input type="text" name="contact_person_phone" id="edit_contact_person_phone"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Address</label>
                                <textarea name="address" id="edit_address" class="form-control form-control-sm" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i data-feather="save" style="width: 14px; height: 14px;"></i> Update Hotel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
