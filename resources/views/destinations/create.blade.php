@extends('layouts.app')
@section('title', 'Add Destination | Travel Shreval')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between d-flex contactapp-title link-dark">
                                <h1>Add Destination</h1>
                                <a href="{{ route('destinations.index') }}" class="btn btn-outline-warning btn-sm">Back</a>
                            </div>
                        </header>

                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="card p-5">
                                    <form action="{{ route('destinations.store') }}" method="POST" id="destinationForm">
                                        @csrf

                                        <div class="row gx-3">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Country</label>
                                                <select name="country" class="form-select">
                                                    <option value="">-- Select Country --</option>
                                                    <option value="Abu Dhabi" {{ old('country') == 'Abu Dhabi' ? 'selected' : '' }}>Abu Dhabi</option>
                                                    <option value="America" {{ old('country') == 'America' ? 'selected' : '' }}>America</option>
                                                    <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>Australia</option>
                                                    <option value="Austria" {{ old('country') == 'Austria' ? 'selected' : '' }}>Austria</option>
                                                    <option value="Azerbaijan" {{ old('country') == 'Azerbaijan' ? 'selected' : '' }}>Azerbaijan</option>
                                                    <option value="Belgium" {{ old('country') == 'Belgium' ? 'selected' : '' }}>Belgium</option>
                                                    <option value="Bhutan" {{ old('country') == 'Bhutan' ? 'selected' : '' }}>Bhutan</option>
                                                    <option value="Cambodia" {{ old('country') == 'Cambodia' ? 'selected' : '' }}>Cambodia</option>
                                                    <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                                                    <option value="Croatia" {{ old('country') == 'Croatia' ? 'selected' : '' }}>Croatia</option>
                                                    <option value="Denmark" {{ old('country') == 'Denmark' ? 'selected' : '' }}>Denmark</option>
                                                    <option value="Dubai" {{ old('country') == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                                                    <option value="Finland" {{ old('country') == 'Finland' ? 'selected' : '' }}>Finland</option>
                                                    <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>France</option>
                                                    <option value="Georgia" {{ old('country') == 'Georgia' ? 'selected' : '' }}>Georgia</option>
                                                    <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>Germany</option>
                                                    <option value="Greece" {{ old('country') == 'Greece' ? 'selected' : '' }}>Greece</option>
                                                    <option value="Hong Kong" {{ old('country') == 'Hong Kong' ? 'selected' : '' }}>Hong Kong</option>
                                                    <option value="Iceland" {{ old('country') == 'Iceland' ? 'selected' : '' }}>Iceland</option>
                                                    <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India</option>
                                                    <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                                    <option value="Ireland" {{ old('country') == 'Ireland' ? 'selected' : '' }}>Ireland</option>
                                                    <option value="Italy" {{ old('country') == 'Italy' ? 'selected' : '' }}>Italy</option>
                                                    <option value="Kazakhstan" {{ old('country') == 'Kazakhstan' ? 'selected' : '' }}>Kazakhstan</option>
                                                    <option value="Laos" {{ old('country') == 'Laos' ? 'selected' : '' }}>Laos</option>
                                                    <option value="Lithuania" {{ old('country') == 'Lithuania' ? 'selected' : '' }}>Lithuania</option>
                                                    <option value="Luxembourg" {{ old('country') == 'Luxembourg' ? 'selected' : '' }}>Luxembourg</option>
                                                    <option value="Macau" {{ old('country') == 'Macau' ? 'selected' : '' }}>Macau</option>
                                                    <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                                    <option value="Mauritius" {{ old('country') == 'Mauritius' ? 'selected' : '' }}>Mauritius</option>
                                                    <option value="Moldova" {{ old('country') == 'Moldova' ? 'selected' : '' }}>Moldova</option>
                                                    <option value="Nepal" {{ old('country') == 'Nepal' ? 'selected' : '' }}>Nepal</option>
                                                    <option value="Netherlands" {{ old('country') == 'Netherlands' ? 'selected' : '' }}>Netherlands</option>
                                                    <option value="New Zealand" {{ old('country') == 'New Zealand' ? 'selected' : '' }}>New Zealand</option>
                                                    <option value="Norway" {{ old('country') == 'Norway' ? 'selected' : '' }}>Norway</option>
                                                    <option value="Phu Quoc" {{ old('country') == 'Phu Quoc' ? 'selected' : '' }}>Phu Quoc</option>
                                                    <option value="Poland" {{ old('country') == 'Poland' ? 'selected' : '' }}>Poland</option>
                                                    <option value="Portugal" {{ old('country') == 'Portugal' ? 'selected' : '' }}>Portugal</option>
                                                    <option value="Russia" {{ old('country') == 'Russia' ? 'selected' : '' }}>Russia</option>
                                                    <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                                                    <option value="Spain" {{ old('country') == 'Spain' ? 'selected' : '' }}>Spain</option>
                                                    <option value="Srilanka" {{ old('country') == 'Srilanka' ? 'selected' : '' }}>Srilanka</option>
                                                    <option value="Sweden" {{ old('country') == 'Sweden' ? 'selected' : '' }}>Sweden</option>
                                                    <option value="Switzerland" {{ old('country') == 'Switzerland' ? 'selected' : '' }}>Switzerland</option>
                                                    <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                                                    <option value="Turkey" {{ old('country') == 'Turkey' ? 'selected' : '' }}>Turkey</option>
                                                    <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                                                    <option value="Vatican City" {{ old('country') == 'Vatican City' ? 'selected' : '' }}>Vatican City</option>
                                                    <option value="Vietnam" {{ old('country') == 'Vietnam' ? 'selected' : '' }}>Vietnam</option>
                                                </select>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Destination Name*</label>
                                                <input type="text" name="name" class="form-control"
                                                    placeholder="Enter destination name" required>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label mb-2">Locations</label>
                                                <div class="border rounded p-3" id="locationsContainer">
                                                    <div class="d-flex flex-wrap gap-2 mb-2" id="locationBadges">
                                                        <!-- Badges will be added here -->
                                                    </div>
                                                    <input type="text" 
                                                        id="locationInput" 
                                                        class="form-control" 
                                                        placeholder="Type location name and press Comma or Enter to add"
                                                        autocomplete="off">
                                                </div>
                                                <small class="text-muted">Type location names and press Comma or Enter to create tags</small>
                                            </div>
                                        </div>

                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-success">Save Destination</button>
                                            <a href="{{ route('destinations.index') }}" class="btn btn-secondary">Cancel</a>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const locationInput = document.getElementById('locationInput');
            const locationBadges = document.getElementById('locationBadges');
            const locations = new Set(); // Track unique locations

            // Function to create a badge
            function createLocationBadge(locationName) {
                if (!locationName || locationName.trim() === '' || locations.has(locationName.trim())) {
                    return false;
                }

                const trimmedName = locationName.trim();
                locations.add(trimmedName);

                const badge = document.createElement('span');
                badge.className = 'badge bg-primary d-inline-flex align-items-center gap-1';
                badge.style.fontSize = '0.875rem';
                badge.innerHTML = `
                    ${trimmedName}
                    <button type="button" class="btn-close btn-close-white" style="font-size: 0.7rem;" data-location="${trimmedName}"></button>
                `;
                locationBadges.appendChild(badge);

                // Add hidden input for form submission
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'locations[]';
                hiddenInput.value = trimmedName;
                locationBadges.appendChild(hiddenInput);

                return true;
            }

            // Function to remove a badge
            function removeLocationBadge(locationName) {
                locations.delete(locationName);
                const badges = locationBadges.querySelectorAll('.badge');
                const hiddenInputs = locationBadges.querySelectorAll('input[type="hidden"]');
                
                badges.forEach(badge => {
                    const btn = badge.querySelector('button[data-location]');
                    if (btn && btn.getAttribute('data-location') === locationName) {
                        badge.remove();
                    }
                });

                hiddenInputs.forEach(input => {
                    if (input.value === locationName) {
                        input.remove();
                    }
                });
            }

            // Handle input events
            locationInput.addEventListener('keydown', function(e) {
                if (e.key === ',' || e.key === 'Enter') {
                    e.preventDefault();
                    let value = this.value.trim();
                    
                    // If comma was pressed, get text before comma
                    if (e.key === ',') {
                        const commaIndex = value.indexOf(',');
                        if (commaIndex !== -1) {
                            value = value.substring(0, commaIndex).trim();
                        }
                    }
                    
                    if (value) {
                        if (createLocationBadge(value)) {
                            this.value = '';
                        }
                    }
                }
            });

            // Handle paste events (split by comma)
            locationInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                const words = pastedText.split(',').filter(word => word.trim() !== '');
                words.forEach(word => createLocationBadge(word.trim()));
                this.value = '';
            });

            // Handle badge removal
            locationBadges.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-close')) {
                    const locationName = e.target.getAttribute('data-location');
                    if (locationName) {
                        removeLocationBadge(locationName);
                    }
                }
            });
        });
    </script>
    @endpush
@endsection
