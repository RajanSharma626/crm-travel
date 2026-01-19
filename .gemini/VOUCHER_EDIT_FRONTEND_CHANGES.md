# Voucher Edit Implementation - Frontend Changes Required

## ✅ COMPLETED:

1. Backend routes added (show, update)
2. Backend controller methods added (show, update)
3. JavaScript edit module created (`public/js/voucher-edit.js`)

## 📝 REMAINING MANUAL CHANGES NEEDED:

### 1. Include the Edit JavaScript File

**Location:** `booking-form.blade.php` - After line 2250 (before @push('scripts'))

**Add:**

```blade
<!-- Voucher Edit Functionality -->
<script src="{{ asset('js/voucher-edit.js') }}"></script>
```

### 2. Add IDs to Create Buttons

**Location:** Lines 1261-1275 in `booking-form.blade.php`

**Change FROM:**

```blade
<button type="button" class="btn btn-sm btn-success"
    onclick="createServiceVoucher()">
```

**Change TO:**

```blade
<button type="button" class="btn btn-sm btn-success" id="createServiceVoucherBtn"
    onclick="createServiceVoucher()" style="display: none;">
```

**Repeat for all three buttons:**

- `id="createServiceVoucherBtn"` - Service button
- `id="createItineraryVoucherBtn"` - Itinerary button
- `id="createAccommodationVoucherBtn"` - Accommodation button

**All should have `style="display: none;"` initially**

### 3. Update Vouchers Table - Add Edit Button

**Location:** Around line 3730 in `updateVouchersTable` function

**In the Action column, BEFORE the download button, add:**

```javascript
<button
    type="button"
    class="btn btn-sm btn-outline-primary"
    onclick="editVoucher(${voucher.id}, '${voucher.voucher_type}', {{ $lead->id }})"
    title="Edit Voucher"
>
    <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
</button>
```

**Note:** Itinerary vouchers don't need edit button (no editable fields)

### 4. Update `updateVouchersTable` Function

**Location:** Around line 3690

**At the END of the function, BEFORE `feather.replace()`, add:**

```javascript
// Update create button visibility based on existing vouchers
updateCreateButtonVisibility(vouchers);
```

**In the empty state (no vouchers), add:**

```javascript
// Show all create buttons if no vouchers exist
showAllCreateButtons();
```

### 5. Update `submitServiceVoucher` Function

**Location:** Around line 3902

**REPLACE the entire function with:**

```javascript
window.submitServiceVoucher = function() {
    // Get data from CKEditor
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
                @if ($isOpsDept ?? false)
                    loadVouchers();
                @endif

                // Reset modal
                resetServiceVoucherModal();
                if (serviceProvidedEditor) {
                    serviceProvidedEditor.setData('');
                }
                document.getElementById('serviceComments').value = '';
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

### 6. Update `submitAccommodationVoucher` Function

**Location:** Around line 3947

**Similar changes as service voucher - check for `voucherId` and use PUT/POST accordingly**

### 7. Reset Modals on Close

**Location:** Around line 3797 (service modal close event)

**Add:**

```javascript
document
    .getElementById("serviceVoucherModal")
    .addEventListener("hidden.bs.modal", function () {
        resetServiceVoucherModal();
        if (serviceProvidedEditor) {
            serviceProvidedEditor.setData("");
        }
        document.getElementById("serviceComments").value = "";
    });
```

**Similar for accommodation modal**

### 8. Update Table Column Widths

**Location:** Around line 1281 (table header)

**Change Action column width from 11% to 15% to accommodate edit button:**

```blade
<th style="width: 15%;" class="text-center">Action</th>
```

## 🎯 TESTING CHECKLIST:

After making these changes, test:

1. ✅ Create service voucher → Button disappears
2. ✅ Create itinerary voucher → Button disappears
3. ✅ Create accommodation voucher for Hotel A → Can still create for B & C
4. ✅ After all hotels have vouchers → Hotel button disappears
5. ✅ Click Edit on service voucher → Modal opens with data
6. ✅ Update service voucher → Saves successfully
7. ✅ Click Edit on accommodation voucher → Modal opens with data
8. ✅ Update accommodation voucher → Saves successfully
9. ✅ Delete voucher → Create button reappears
10. ✅ Refresh page → Button visibility persists correctly

## 📌 KEY POINTS:

- **Itinerary vouchers**: No edit button (no editable fields)
- **Service & Accommodation**: Full edit capability
- **Button visibility**: Managed automatically by `updateCreateButtonVisibility()`
- **Modal state**: Always reset to "Create" mode on close
- **Edit mode**: Detected by presence of `dataset.voucherId`

## 🔧 QUICK REFERENCE:

**Button IDs:**

- `createServiceVoucherBtn`
- `createItineraryVoucherBtn`
- `createAccommodationVoucherBtn`

**Functions:**

- `editVoucher(id, type, leadId)` - Open edit modal
- `updateCreateButtonVisibility(vouchers)` - Show/hide create buttons
- `showAllCreateButtons()` - Show all buttons
- `resetServiceVoucherModal()` - Reset to create mode
- `resetAccommodationVoucherModal()` - Reset to create mode
