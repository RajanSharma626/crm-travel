# Voucher Edit Functionality Implementation Guide

## Objective

Implement edit functionality for vouchers so that:

1. Only ONE voucher of each type can be created (service, itinerary, accommodation per hotel)
2. After creation, users can only EDIT existing vouchers, not create duplicates
3. Delete functionality remains available

## Changes Required

### 1. Update Voucher Management Buttons (booking-form.blade.php, lines 1260-1276)

**Current State:**

- All three create buttons are always visible
- No check for existing vouchers

**Required Changes:**

```blade
<div class="btn-group" role="group" id="voucherCreateButtons">
    <button type="button" class="btn btn-sm btn-success" id="createServiceVoucherBtn"
        onclick="createServiceVoucher()" style="display: none;">
        <i data-feather="file-plus" style="width: 14px; height: 14px;"></i>
        Service Voucher
    </button>
    <button type="button" class="btn btn-sm btn-info" id="createItineraryVoucherBtn"
        onclick="createItineraryVoucher()" style="display: none;">
        <i data-feather="map" style="width: 14px; height: 14px;"></i>
        Itinerary
    </button>
    <button type="button" class="btn btn-sm btn-warning" id="createAccommodationVoucherBtn"
        data-bs-toggle="modal" data-bs-target="#accommodationVoucherModal" style="display: none;">
        <i data-feather="home" style="width: 14px; height: 14px;"></i>
        Hotel Voucher
    </button>
</div>
```

### 2. Update Vouchers Table Actions Column

**Add Edit Button** in the Action column (around line 1287):

```blade
<th style="width: 15%;" class="text-center">Action</th>
```

### 3. Update JavaScript updateVouchersTable Function (around line 3690)

**Current:** Only shows Download and Delete buttons
**Required:** Add Edit button and logic to show/hide create buttons

```javascript
function updateVouchersTable(vouchers) {
    const tbody = document.getElementById("vouchersTableBody");

    // Track which voucher types exist
    let hasServiceVoucher = false;
    let hasItineraryVoucher = false;
    let accommodationVouchers = new Set();

    if (!vouchers || vouchers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i data-feather="inbox" style="width: 24px; height: 24px; opacity: 0.5;" class="mb-2"></i>
                    <div>No vouchers created yet</div>
                </td>
            </tr>
        `;
        feather.replace();

        // Show all create buttons if no vouchers exist
        showAllCreateButtons();
        return;
    }

    let html = "";
    vouchers.forEach((voucher) => {
        // Track voucher types
        if (voucher.voucher_type === "service") hasServiceVoucher = true;
        if (voucher.voucher_type === "itinerary") hasItineraryVoucher = true;
        if (
            voucher.voucher_type === "accommodation" &&
            voucher.accommodation_id
        ) {
            accommodationVouchers.add(voucher.accommodation_id);
        }

        const typeLabel =
            voucher.voucher_type === "service"
                ? "Service"
                : voucher.voucher_type === "itinerary"
                  ? "Itinerary"
                  : "Accommodation";
        const typeBadge =
            voucher.voucher_type === "service"
                ? "success"
                : voucher.voucher_type === "itinerary"
                  ? "info"
                  : "warning";
        const location = voucher.accommodation
            ? voucher.accommodation.stay_at
            : "-";

        // Truncate service and comments for display
        const service = voucher.service_provided || "-";
        const serviceTruncated =
            service.length > 50 ? service.substring(0, 50) + "..." : service;

        const comments = voucher.comments || "-";
        const commentsTruncated =
            comments.length > 40 ? comments.substring(0, 40) + "..." : comments;

        const createdBy = voucher.created_by
            ? voucher.created_by.name || "Unknown"
            : "System";
        const createdAt = new Date(voucher.created_at).toLocaleString("en-IN", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });

        html += `
            <tr>
                <td><strong>${voucher.voucher_number}</strong></td>
                <td><span class="badge bg-${typeBadge}">${typeLabel}</span></td>
                <td>${location}</td>
                <td title="${service.replace(/"/g, "&quot;")}" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <small>${serviceTruncated}</small>
                </td>
                <td title="${comments.replace(/"/g, "&quot;")}" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <small>${commentsTruncated}</small>
                </td>
                <td><small>${createdBy}</small></td>
                <td><small>${createdAt}</small></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="editVoucher(${voucher.id}, '${voucher.voucher_type}')" title="Edit Voucher">
                        <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                    </button>
                    <a href="{{ url('/leads') }}/${voucher.lead_id}/vouchers/${voucher.id}/download" 
                        class="btn btn-sm btn-outline-success" target="_blank" title="Download Voucher">
                        <i data-feather="download" style="width: 14px; height: 14px;"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                        onclick="deleteVoucher(${voucher.id})" title="Delete Voucher">
                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    feather.replace();

    // Update create button visibility
    updateCreateButtonVisibility(
        hasServiceVoucher,
        hasItineraryVoucher,
        accommodationVouchers,
    );
}
```

### 4. Add New JavaScript Functions

```javascript
// Show/hide create buttons based on existing vouchers
function updateCreateButtonVisibility(hasServiceVoucher, hasItineraryVoucher, accommodationVouchers) {
    // Service voucher button
    const serviceBtn = document.getElementById('createServiceVoucherBtn');
    if (serviceBtn) {
        serviceBtn.style.display = hasServiceVoucher ? 'none' : 'inline-block';
    }

    // Itinerary voucher button
    const itineraryBtn = document.getElementById('createItineraryVoucherBtn');
    if (itineraryBtn) {
        itineraryBtn.style.display = hasItineraryVoucher ? 'none' : 'inline-block';
    }

    // Accommodation voucher button - check if all hotels have vouchers
    const totalAccommodations = {{ $lead->bookingAccommodations->count() ?? 0 }};
    const accommodationBtn = document.getElementById('createAccommodationVoucherBtn');
    if (accommodationBtn) {
        accommodationBtn.style.display = (accommodationVouchers.size >= totalAccommodations) ? 'none' : 'inline-block';
    }

    feather.replace();
}

// Show all create buttons (when no vouchers exist)
function showAllCreateButtons() {
    document.getElementById('createServiceVoucherBtn').style.display = 'inline-block';
    document.getElementById('createItineraryVoucherBtn').style.display = 'inline-block';
    document.getElementById('createAccommodationVoucherBtn').style.display = 'inline-block';
    feather.replace();
}

// Edit voucher function
window.editVoucher = function(voucherId, voucherType) {
    // Fetch voucher data
    fetch(`{{ url('/leads') }}/{{ $lead->id }}/vouchers/${voucherId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const voucher = data.voucher;

                if (voucherType === 'service') {
                    // Populate service voucher modal with existing data
                    if (serviceProvidedEditor) {
                        serviceProvidedEditor.setData(voucher.service_provided || '');
                    }
                    document.getElementById('serviceComments').value = voucher.comments || '';

                    // Store voucher ID for update
                    document.getElementById('serviceVoucherForm').dataset.voucherId = voucherId;

                    // Change modal title and button text
                    document.getElementById('serviceVoucherModalLabel').textContent = 'Edit Service Voucher';

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('serviceVoucherModal'));
                    modal.show();
                } else if (voucherType === 'accommodation') {
                    // Similar logic for accommodation voucher
                    // ... (implement similar to service)
                }
                // Itinerary vouchers don't have editable fields, so no edit modal needed
            }
        })
        .catch(error => {
            console.error('Error fetching voucher:', error);
            alert('Error loading voucher data');
        });
};
```

### 5. Update Submit Functions to Handle Both Create and Update

```javascript
window.submitServiceVoucher = function() {
    const serviceProvided = serviceProvidedEditor ? serviceProvidedEditor.getData().trim() : '';
    const comments = document.getElementById('serviceComments').value.trim();
    const voucherId = document.getElementById('serviceVoucherForm').dataset.voucherId;

    if (!serviceProvided) {
        alert('Please enter service provided');
        return;
    }

    if (!comments) {
        alert('Please enter comments');
        return;
    }

    const url = voucherId
        ? `{{ url('/leads') }}/{{ $lead->id }}/vouchers/${voucherId}`
        : '{{ route('vouchers.create-service', $lead) }}';

    const method = voucherId ? 'PUT' : 'POST';

    fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                service_provided: serviceProvided,
                comments: comments
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(voucherId ? 'Service voucher updated successfully!' : 'Service voucher created successfully!');
                bootstrap.Modal.getInstance(document.getElementById('serviceVoucherModal')).hide();
                loadVouchers();

                // Clear form and voucher ID
                if (serviceProvidedEditor) {
                    serviceProvidedEditor.setData('');
                }
                document.getElementById('serviceComments').value = '';
                delete document.getElementById('serviceVoucherForm').dataset.voucherId;
                document.getElementById('serviceVoucherModalLabel').textContent = 'Create Service Voucher';
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the voucher');
        });
};
```

### 6. Add Backend Routes for Edit/Update

**routes/web.php:**

```php
// Add these routes in the operations middleware group
Route::get('/leads/{lead}/vouchers/{voucher}', [VoucherController::class, 'show'])
    ->name('vouchers.show');
Route::put('/leads/{lead}/vouchers/{voucher}', [VoucherController::class, 'update'])
    ->name('vouchers.update');
```

### 7. Add Backend Controller Methods

**VoucherController.php:**

```php
public function show(Lead $lead, Voucher $voucher)
{
    // Ensure voucher belongs to this lead
    if ($voucher->lead_id !== $lead->id) {
        abort(404);
    }

    $voucher->load('accommodation', 'createdBy');

    return response()->json([
        'success' => true,
        'voucher' => $voucher
    ]);
}

public function update(Request $request, Lead $lead, Voucher $voucher)
{
    // Ensure voucher belongs to this lead
    if ($voucher->lead_id !== $lead->id) {
        abort(404);
    }

    // Validate based on voucher type
    if ($voucher->voucher_type === 'service' || $voucher->voucher_type === 'accommodation') {
        $validated = $request->validate([
            'service_provided' => 'required|string',
            'comments' => 'required|string',
        ]);

        $voucher->update($validated);
    }

    return response()->json([
        'success' => true,
        'message' => 'Voucher updated successfully',
        'voucher' => $voucher->fresh(['accommodation', 'createdBy'])
    ]);
}
```

## Summary

This implementation will:

1. ✅ Hide create buttons after vouchers are created
2. ✅ Add edit buttons to existing vouchers
3. ✅ Allow users to update service_provided and comments
4. ✅ Prevent duplicate voucher creation
5. ✅ Maintain delete functionality
6. ✅ Show create buttons only for missing voucher types

The user experience will be:

- **First time**: All create buttons visible
- **After creating service voucher**: Service button hidden, edit button appears in table
- **After creating itinerary**: Itinerary button hidden
- **After creating all hotel vouchers**: Hotel button hidden
- **Editing**: Click edit button → modal opens with existing data → update → saved
