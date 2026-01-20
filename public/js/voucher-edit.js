// Voucher Edit Functionality
// This file handles editing existing vouchers and managing create button visibility

// Track CKEditor instances (defined in main file, referenced here)
// let serviceProvidedEditor = null;
// let accommodationServiceEditor = null;

// Current voucher being edited
let currentEditingVoucher = null;

/**
 * Update create button visibility based on existing vouchers
 */
function updateCreateButtonVisibility(vouchers) {
    if (!vouchers) return;

    // Track which voucher types exist
    let hasServiceVoucher = false;
    let hasItineraryVoucher = false;
    let accommodationVouchers = new Set();

    vouchers.forEach((voucher) => {
        if (voucher.voucher_type === "service") hasServiceVoucher = true;
        if (voucher.voucher_type === "itinerary") hasItineraryVoucher = true;
        if (
            voucher.voucher_type === "accommodation" &&
            voucher.accommodation_id
        ) {
            accommodationVouchers.add(voucher.accommodation_id);
        }
    });

    // Service voucher button
    const serviceBtn = document.getElementById("createServiceVoucherBtn");
    if (serviceBtn) {
        serviceBtn.style.display = hasServiceVoucher ? "none" : "inline-block";
    }

    // Itinerary button
    const itineraryBtn = document.getElementById("createItineraryVoucherBtn");
    if (itineraryBtn) {
        itineraryBtn.style.display = hasItineraryVoucher
            ? "none"
            : "inline-block";
    }

    // Accommodation voucher button - check if all hotels have vouchers
    const accommodationBtn = document.getElementById(
        "createAccommodationVoucherBtn",
    );
    if (accommodationBtn) {
        // Get total accommodations from the modal list
        const accommodationItems = document.querySelectorAll(
            ".accommodation-item",
        );
        const totalAccommodations = accommodationItems.length;
        accommodationBtn.style.display =
            accommodationVouchers.size >= totalAccommodations
                ? "none"
                : "inline-block";
    }

    // Replace feather icons
    if (typeof feather !== "undefined") {
        feather.replace();
    }
}

/**
 * Show all create buttons (when no vouchers exist)
 */
function showAllCreateButtons() {
    const serviceBtn = document.getElementById("createServiceVoucherBtn");
    const itineraryBtn = document.getElementById("createItineraryVoucherBtn");
    const accommodationBtn = document.getElementById(
        "createAccommodationVoucherBtn",
    );

    if (serviceBtn) serviceBtn.style.display = "inline-block";
    if (itineraryBtn) itineraryBtn.style.display = "inline-block";
    if (accommodationBtn) accommodationBtn.style.display = "inline-block";

    if (typeof feather !== "undefined") {
        feather.replace();
    }
}

/**
 * Edit voucher function
 */
window.editVoucher = function (voucherId, voucherType, leadId) {
    // Fetch voucher data
    fetch(`/leads/${leadId}/vouchers/${voucherId}`)
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                const voucher = data.voucher;
                currentEditingVoucher = voucher;

                if (voucherType === "service") {
                    // Populate service voucher modal with existing data
                    if (
                        typeof serviceProvidedEditor !== "undefined" &&
                        serviceProvidedEditor
                    ) {
                        serviceProvidedEditor.setData(
                            voucher.service_provided || "",
                        );
                    } else {
                        document.getElementById("serviceProvided").value =
                            voucher.service_provided || "";
                    }
                    document.getElementById("serviceComments").value =
                        voucher.comments || "";

                    // Store voucher ID for update
                    document.getElementById(
                        "serviceVoucherForm",
                    ).dataset.voucherId = voucherId;

                    // Change modal title and button text
                    document.getElementById(
                        "serviceVoucherModalLabel",
                    ).textContent = "Edit Service Voucher";
                    const submitBtn = document.querySelector(
                        "#serviceVoucherModal .btn-success",
                    );
                    if (submitBtn) {
                        submitBtn.innerHTML =
                            '<i data-feather="check" style="width: 14px; height: 14px;"></i> Update Voucher';
                        if (typeof feather !== "undefined") feather.replace();
                    }

                    // Show modal
                    const modal = new bootstrap.Modal(
                        document.getElementById("serviceVoucherModal"),
                    );
                    modal.show();
                } else if (voucherType === "accommodation") {
                    // Populate accommodation voucher modal
                    if (
                        typeof accommodationServiceEditor !== "undefined" &&
                        accommodationServiceEditor
                    ) {
                        accommodationServiceEditor.setData(
                            voucher.service_provided || "",
                        );
                    } else {
                        document.getElementById(
                            "accommodationServiceProvided",
                        ).value = voucher.service_provided || "";
                    }
                    document.getElementById("accommodationComments").value =
                        voucher.comments || "";

                    // Store voucher ID
                    document.getElementById(
                        "accommodationVoucherForm",
                    ).dataset.voucherId = voucherId;

                    // Set selected accommodation
                    document.getElementById("selectedAccommodationId").value =
                        voucher.accommodation_id;

                    // Show the form
                    document.getElementById(
                        "accommodationVoucherForm",
                    ).style.display = "block";
                    document.getElementById(
                        "submitAccommodationVoucherBtn",
                    ).style.display = "inline-block";

                    // Highlight selected accommodation
                    document
                        .querySelectorAll(".accommodation-item")
                        .forEach((item) => {
                            item.classList.remove("active");
                            if (
                                parseInt(item.dataset.accommodationId) ===
                                voucher.accommodation_id
                            ) {
                                item.classList.add("active");
                            }
                        });

                    // Change modal title
                    const modalLabel = document.querySelector(
                        "#accommodationVoucherModal .modal-title",
                    );
                    if (modalLabel) {
                        modalLabel.textContent = "Edit Accommodation Voucher";
                    }

                    // Show modal
                    const modal = new bootstrap.Modal(
                        document.getElementById("accommodationVoucherModal"),
                    );
                    modal.show();
                }
            } else {
                alert(
                    "Error loading voucher data: " +
                        (data.message || "Unknown error"),
                );
            }
        })
        .catch((error) => {
            console.error("Error fetching voucher:", error);
            alert("Error loading voucher data");
        });
};

/**
 * Reset modal to create mode
 */
function resetServiceVoucherModal() {
    delete document.getElementById("serviceVoucherForm").dataset.voucherId;
    document.getElementById("serviceVoucherModalLabel").textContent =
        "Create Service Voucher";
    const submitBtn = document.querySelector(
        "#serviceVoucherModal .btn-success",
    );
    if (submitBtn) {
        submitBtn.innerHTML =
            '<i data-feather="check" style="width: 14px; height: 14px;"></i> Create Voucher';
        if (typeof feather !== "undefined") feather.replace();
    }
    currentEditingVoucher = null;
}

function resetAccommodationVoucherModal() {
    delete document.getElementById("accommodationVoucherForm").dataset
        .voucherId;
    const modalLabel = document.querySelector(
        "#accommodationVoucherModal .modal-title",
    );
    if (modalLabel) {
        modalLabel.textContent = "Create Accommodation Voucher";
    }
    currentEditingVoucher = null;
}

// Export functions for use in main script
window.updateCreateButtonVisibility = updateCreateButtonVisibility;
window.showAllCreateButtons = showAllCreateButtons;
window.resetServiceVoucherModal = resetServiceVoucherModal;
window.resetAccommodationVoucherModal = resetAccommodationVoucherModal;
