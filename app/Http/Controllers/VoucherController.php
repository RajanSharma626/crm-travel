<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Voucher;
use App\Models\Operation;
use App\Models\BookingAccommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class VoucherController extends Controller
{
    /**
     * Display a listing of vouchers for a lead
     */
    public function index(Lead $lead)
    {
        $vouchers = $lead->vouchers()
            ->with(['accommodation', 'createdBy'])
            ->latest()
            ->get();
        
        return response()->json([
            'success' => true,
            'vouchers' => $vouchers
        ]);
    }

    /**
     * Create Service Voucher
     * Requires: service_provided and comments
     */
    public function createServiceVoucher(Request $request, Lead $lead)
    {
        // Check if user is operations
        if (!Auth::user()->hasAnyRole(['Admin', 'Operations', 'Operation', 'Operation Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'voucher_number' => 'nullable|string|max:50|unique:vouchers,voucher_number',
            'emergency_contact_number' => 'nullable|string|max:20',
            'service_provided' => 'required|string',
            'comments' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Get or create operation
            $operation = $lead->operation;
            if (!$operation) {
                $operation = Operation::create([
                    'lead_id' => $lead->id,
                    'operation_status' => 'in_progress',
                ]);
            }

            // Use provided voucher number or generate one
            $voucherNumber = $validated['voucher_number'] ?? Voucher::generateVoucherNumber('service');

            // Update operation with voucher number if not set
            if (!$operation->voucher_number) {
                $operation->update(['voucher_number' => $voucherNumber]);
            }

            // Create voucher
            $voucher = Voucher::create([
                'lead_id' => $lead->id,
                'operation_id' => $operation->id,
                'voucher_type' => 'service',
                'voucher_number' => $voucherNumber,
                'service_provided' => $validated['service_provided'],
                'comments' => $validated['comments'] ?? null,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service voucher created successfully',
                'voucher' => $voucher->load('createdBy'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating service voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create Itinerary
     * No service or comment input required - directly created
     */
    public function createItineraryVoucher(Request $request, Lead $lead)
    {
        // Check if user is operations
        if (!Auth::user()->hasAnyRole(['Admin', 'Operations', 'Operation', 'Operation Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // Get or create operation
            $operation = $lead->operation;
            if (!$operation) {
                $operation = Operation::create([
                    'lead_id' => $lead->id,
                    'operation_status' => 'in_progress',
                ]);
            }

            // Generate voucher number
            $voucherNumber = Voucher::generateVoucherNumber('itinerary');

            // Update operation with voucher number if not set
            if (!$operation->voucher_number) {
                $operation->update(['voucher_number' => $voucherNumber]);
            }

            // Create voucher
            $voucher = Voucher::create([
                'lead_id' => $lead->id,
                'operation_id' => $operation->id,
                'voucher_type' => 'itinerary',
                'voucher_number' => $voucherNumber,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Itinerary created successfully',
                'voucher' => $voucher->load('createdBy'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating itinerary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create Accommodation Voucher for specific hotel
     * Requires: accommodation_id, service_provided, and comments
     */
    public function createAccommodationVoucher(Request $request, Lead $lead)
    {
        // Check if user is operations
        if (!Auth::user()->hasAnyRole(['Admin', 'Operations', 'Operation', 'Operation Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'voucher_number' => 'nullable|string|max:50|unique:vouchers,voucher_number',
            'emergency_contact_number' => 'nullable|string|max:20',
            'accommodation_id' => 'required|exists:booking_accommodations,id',
            'service_provided' => 'required|string',
            'comments' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Verify accommodation belongs to this lead
            $accommodation = BookingAccommodation::where('id', $validated['accommodation_id'])
                ->where('lead_id', $lead->id)
                ->firstOrFail();

            // Get or create operation
            $operation = $lead->operation;
            if (!$operation) {
                $operation = Operation::create([
                    'lead_id' => $lead->id,
                    'operation_status' => 'in_progress',
                ]);
            }

            // Use provided voucher number or generate one
            $voucherNumber = $validated['voucher_number'] ?? Voucher::generateVoucherNumber('accommodation');

            // Update operation with voucher number if not set
            if (!$operation->voucher_number) {
                $operation->update(['voucher_number' => $voucherNumber]);
            }

            // Create voucher
            $voucher = Voucher::create([
                'lead_id' => $lead->id,
                'operation_id' => $operation->id,
                'voucher_type' => 'accommodation',
                'voucher_number' => $voucherNumber,
                'accommodation_id' => $validated['accommodation_id'],
                'service_provided' => $validated['service_provided'],
                'comments' => $validated['comments'] ?? null,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Accommodation voucher created successfully',
                'voucher' => $voucher->load(['createdBy', 'accommodation']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating accommodation voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download voucher PDF
     */
    public function downloadVoucher(Lead $lead, Voucher $voucher, Request $request)
    {
        // Check if user has permission
        if (!Auth::user()->hasAnyRole(['Admin', 'Delivery', 'Delivery Manager', 'Operations', 'Operation', 'Operation Manager']) && Auth::user()->department !== 'Delivery' && Auth::user()->department !== 'Operations') {
            abort(403, 'Unauthorized');
        }

        // Verify voucher belongs to lead
        if ($voucher->lead_id !== $lead->id) {
            abort(404, 'Voucher not found for this lead');
        }

        // Check for with_company_details parameter (default to 1/true)
        $withCompanyDetails = $request->query('with_company_details', '1');

        // Load necessary relationships
        $lead->load([
            'service',
            'destination',
            'assignedUser',
            'bookingDestinations',
            'bookingArrivalDepartures',
            'bookingItineraries',
            'bookingAccommodations',
            'operation'
        ]);

        // Get logo
        $logoPath = public_path('dist/img/transparency Royal Blue 2-01.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        // Generate PDF based on voucher type
        if ($voucher->voucher_type === 'itinerary') {
            $pdf = Pdf::loadView('pdf.itinerary', compact('lead', 'logoBase64', 'voucher', 'withCompanyDetails'));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('Itinerary_' . $lead->tsq . '_' . $voucher->voucher_number . '.pdf');
        } elseif ($voucher->voucher_type === 'service') {
            $pdf = Pdf::loadView('pdf.service-voucher', compact('lead', 'logoBase64', 'voucher', 'withCompanyDetails'));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('Service_Voucher_' . $lead->tsq . '_' . $voucher->voucher_number . '.pdf');
        } elseif ($voucher->voucher_type === 'accommodation') {
            $accommodation = $voucher->accommodation;
            $pdf = Pdf::loadView('pdf.destination-voucher', compact('lead', 'logoBase64', 'voucher', 'accommodation', 'withCompanyDetails'));
            $pdf->setPaper('A4', 'portrait');
            
            $location = $accommodation->location ?? 'Accommodation';
            $locationSlug = str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $location));
            return $pdf->download($locationSlug . '_' . $lead->tsq . '_' . $voucher->voucher_number . '.pdf');
        }

        abort(404, 'Invalid voucher type');
    }

    /**
     * Delete a voucher
     */
    /**
     * Show a specific voucher
     */
    public function show(Lead $lead, Voucher $voucher)
    {
        // Check if user is operations or admin
        if (!Auth::user()->hasAnyRole(['Admin', 'Operations', 'Operation', 'Operation Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify voucher belongs to lead
        if ($voucher->lead_id !== $lead->id) {
            return response()->json(['error' => 'Voucher not found for this lead'], 404);
        }

        $voucher->load('accommodation', 'createdBy');

        return response()->json([
            'success' => true,
            'voucher' => $voucher
        ]);
    }

    /**
     * Update an existing voucher
     */
    public function update(Request $request, Lead $lead, Voucher $voucher)
    {
        // Check if user is operations or admin
        if (!Auth::user()->hasAnyRole(['Admin', 'Operations', 'Operation', 'Operation Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify voucher belongs to lead
        if ($voucher->lead_id !== $lead->id) {
            return response()->json(['error' => 'Voucher not found for this lead'], 404);
        }

        // Validate based on voucher type
        if ($voucher->voucher_type === 'service' || $voucher->voucher_type === 'accommodation') {
            $rules = [
                'service_provided' => 'required|string',
                'comments' => 'nullable|string',
                'emergency_contact_number' => 'nullable|string|max:20',
                'voucher_number' => 'required|string|max:50|unique:vouchers,voucher_number,' . $voucher->id,
            ];

            $validated = $request->validate($rules);

            try {
                $voucher->update($validated);

                return response()->json([
                    'success' => true,
                    'message' => 'Voucher updated successfully',
                    'voucher' => $voucher->fresh(['accommodation', 'createdBy'])
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating voucher: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'This voucher type cannot be edited'
        ], 400);
    }

    public function destroy(Lead $lead, Voucher $voucher)
    {
        // Check if user is operations or admin
        if (!Auth::user()->hasAnyRole(['Admin', 'Operations', 'Operation', 'Operation Manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify voucher belongs to lead
        if ($voucher->lead_id !== $lead->id) {
            return response()->json(['error' => 'Voucher not found for this lead'], 404);
        }

        try {
            $voucher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Voucher deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting voucher: ' . $e->getMessage()
            ], 500);
        }
    }
}
