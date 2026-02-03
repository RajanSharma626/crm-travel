<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadRemarkController;
use App\Http\Controllers\BookingFileRemarkController;
use App\Http\Controllers\ServiceController;
// use App\Http\Controllers\UserController; // Removed - Now using HR tab for user management
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CostComponentController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TravellerDocumentController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\IncentiveRuleController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\HotelController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.auth');

Route::middleware(['auth', 'check.active'])->group(function () {

    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('home');

    // Dashboard route - no permission middleware, only auth required
    Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Users routes removed - Now using HR tab to create user profiles and login details
    // Route::middleware('permission:view users')->group(function () {
    //     Route::get('/users', [UserController::class, 'index'])->name('users');
    // });
    // 
    // Route::middleware('permission:create users')->group(function () {
    //     Route::post('/user/store', [UserController::class, 'store'])->name('users.store');
    // });
    // 
    // Route::middleware('permission:edit users')->group(function () {
    //     Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    //     Route::post('/user/update', [UserController::class, 'update'])->name('users.update');
    // });
    // 
    // Route::middleware('permission:delete users')->group(function () {
    //     Route::get('/user/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
    // });

    // Services - Accessible to Admin and Operations only
    Route::middleware(['admin.ops'])->group(function () {
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        // Redirect create and edit routes to index since forms are now on index page
        Route::get('/services/create', function () {
            return redirect()->route('services.index');
        })->name('services.create');
        Route::get('/services/{service}/edit', function () {
            return redirect()->route('services.index');
        })->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    });

    // Destinations - Accessible to Admin and Operations only
    Route::middleware(['admin.ops'])->group(function () {
        Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
        Route::get('/destinations/create', [DestinationController::class, 'create'])->name('destinations.create');
        Route::post('/destinations', [DestinationController::class, 'store'])->name('destinations.store');
        Route::get('/destinations/{destination}/edit', [DestinationController::class, 'edit'])->name('destinations.edit');
        Route::put('/destinations/{destination}', [DestinationController::class, 'update'])->name('destinations.update');
        Route::delete('/destinations/{destination}', [DestinationController::class, 'destroy'])->name('destinations.destroy');
        Route::get('/destinations/{destination}', [DestinationController::class, 'show'])->name('destinations.show');
    });

    // Hotels - Accessible to Admin and Operations only
    Route::middleware(['admin.ops'])->group(function () {
        Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
        Route::get('/hotels/create', [HotelController::class, 'create'])->name('hotels.create');
        Route::post('/hotels', [HotelController::class, 'store'])->name('hotels.store');
        Route::get('/hotels/{hotel}/edit', [HotelController::class, 'edit'])->name('hotels.edit');
        Route::put('/hotels/{hotel}', [HotelController::class, 'update'])->name('hotels.update');
        Route::delete('/hotels/{hotel}', [HotelController::class, 'destroy'])->name('hotels.destroy');
        Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');

        // API routes for dynamic dropdowns (Hotels)
        Route::get('/api/hotels/destinations/{country}', [HotelController::class, 'getDestinationsByCountry'])->name('api.hotels.destinations');
        Route::get('/api/hotels/locations/{destinationId}', [HotelController::class, 'getLocationsByDestination'])->name('api.hotels.locations');
    });

    // Customer Care Routes
    Route::prefix('customer-care')->name('customer-care.')->middleware(['auth', 'check.active'])->group(function () {
        Route::get('/leads', [\App\Http\Controllers\CustomerCareController::class, 'index'])->name('leads.index');
        Route::get('/leads/create', [\App\Http\Controllers\CustomerCareController::class, 'create'])->name('leads.create');
        Route::post('/leads', [\App\Http\Controllers\CustomerCareController::class, 'store'])->name('leads.store');
        Route::get('/leads/{lead}/edit', [\App\Http\Controllers\CustomerCareController::class, 'edit'])->name('leads.edit');
        Route::put('/leads/{lead}', [\App\Http\Controllers\CustomerCareController::class, 'update'])->name('leads.update');
        Route::get('/leads/{lead}', [\App\Http\Controllers\CustomerCareController::class, 'show'])->name('leads.show');
        Route::delete('/leads/{lead}', [\App\Http\Controllers\CustomerCareController::class, 'destroy'])->name('leads.destroy');

        // Lead Remarks in Customer Care
        Route::get('/leads/{lead}/remarks', [LeadRemarkController::class, 'index'])->name('leads.remarks.index');
        Route::post('/leads/{lead}/remarks', [LeadRemarkController::class, 'store'])->name('leads.remarks.store');
        Route::put('/leads/{lead}/remarks/{remark}', [LeadRemarkController::class, 'update'])->name('leads.remarks.update');

        // Lead Status & Assignment in Customer Care
        Route::post('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.updateStatus');
        Route::post('/leads/{lead}/assign-user', [LeadController::class, 'updateAssignedUser'])->name('leads.assign-user');
        Route::post('/leads/{lead}/reassign', [LeadController::class, 'updateReassignedUser'])->name('leads.reassign');
        Route::post('/leads/bulk-assign', [LeadController::class, 'bulkAssign'])->name('leads.bulkAssign');
    });

    // Leads routes - IMPORTANT: Specific routes must come before wildcard routes
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/bookings', [LeadController::class, 'bookings'])->name('bookings.index');

    Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');

    Route::get('/bookings/{lead}/form', [LeadController::class, 'bookingForm'])->name('bookings.form');

    Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');

    // Vendor Payment routes (Ops only)
    Route::post('/bookings/{lead}/vendor-payment', [LeadController::class, 'storeVendorPayment'])->name('bookings.vendor-payment.store');
    Route::put('/bookings/{lead}/vendor-payment/{vendorPayment}', [LeadController::class, 'updateVendorPayment'])->name('bookings.vendor-payment.update');
    Route::delete('/bookings/{lead}/vendor-payment/{vendorPayment}', [LeadController::class, 'destroyVendorPayment'])->name('bookings.vendor-payment.destroy');


    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');

    // Kept empty middleware group

    Route::post('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.updateStatus');


    Route::post('/leads/{lead}/assign-user', [LeadController::class, 'updateAssignedUser'])->name('leads.updateAssignedUser');
    Route::post('/leads/{lead}/reassign', [LeadController::class, 'updateReassignedUser'])->name('leads.reassign');
    Route::post('/leads/bulk-assign', [LeadController::class, 'bulkAssign'])->name('leads.bulkAssign');


    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');


    // Lead Remarks
    Route::get('/leads/{lead}/remarks', [LeadRemarkController::class, 'index'])->name('leads.remarks.index');
    Route::post('/leads/{lead}/remarks', [LeadRemarkController::class, 'store'])->name('leads.remarks.store');
    Route::put('/leads/{lead}/remarks/{remark}', [LeadRemarkController::class, 'update'])->name('leads.remarks.update');
    Route::delete('/leads/{lead}/remarks/{remark}', [LeadRemarkController::class, 'destroy'])->name('leads.remarks.destroy');

    // SMS Routes
    Route::post('/leads/sms/send', [\App\Http\Controllers\LeadsSmsController::class, 'sendSms'])->name('leads.sms.send');
    Route::get('/leads/sms/templates', [\App\Http\Controllers\LeadsSmsController::class, 'getTemplates'])->name('leads.sms.templates');
    Route::post('/leads/sms/send-custom', [\App\Http\Controllers\LeadsSmsController::class, 'sendCustomSms'])->name('leads.sms.send-custom');

    // Booking File Remarks
    // Booking File Remarks - Moved out of strict permission middleware to allow access based on association
    Route::get('/leads/{lead}/booking-file-remarks', [BookingFileRemarkController::class, 'index'])->name('leads.booking-file-remarks.index');
    Route::post('/leads/{lead}/booking-file-remarks', [BookingFileRemarkController::class, 'store'])->name('leads.booking-file-remarks.store');
    Route::put('/leads/{lead}/booking-file-remarks/{bookingFileRemark}', [BookingFileRemarkController::class, 'update'])->name('leads.booking-file-remarks.update');
    Route::delete('/leads/{lead}/booking-file-remarks/{bookingFileRemark}', [BookingFileRemarkController::class, 'destroy'])->name('leads.booking-file-remarks.destroy');

    // Accounts & Payments
    // Accounts - Self-authorized or Accounts permission
    Route::get('/accounts', [PaymentController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/{lead}/booking-file', [PaymentController::class, 'bookingFile'])->name('accounts.booking-file');
    Route::get('/accounts/leads', [PaymentController::class, 'accountsLeads'])->name('accounts.leads');

    // Accounts Routes
    Route::post('/accounts/{lead}/account-summary', [PaymentController::class, 'storeAccountSummary'])->name('accounts.account-summary.store');
    Route::put('/accounts/{lead}/account-summary/{accountSummary}', [PaymentController::class, 'updateAccountSummary'])->name('accounts.account-summary.update');
    Route::delete('/accounts/{lead}/account-summary/{accountSummary}', [PaymentController::class, 'destroyAccountSummary'])->name('accounts.account-summary.destroy');
    Route::put('/accounts/{lead}/vendor-payment/{vendorPayment}', [PaymentController::class, 'updateVendorPaymentAccounts'])->name('accounts.vendor-payment.update');

    Route::get('/api/accounts/dashboard', [PaymentController::class, 'dashboard'])->name('api.accounts.dashboard');
    Route::get('/api/accounts/leads', [PaymentController::class, 'leads'])->name('api.accounts.leads');
    Route::get('/api/accounts/export', [PaymentController::class, 'export'])->name('api.accounts.export');

    Route::post('/accounts/{lead}/account-summary', [PaymentController::class, 'storeAccountSummary'])->name('accounts.account-summary.store');

    Route::put('/accounts/{lead}/account-summary/{accountSummary}', [PaymentController::class, 'updateAccountSummary'])->name('accounts.account-summary.update');

    Route::delete('/accounts/{lead}/account-summary/{accountSummary}', [PaymentController::class, 'destroyAccountSummary'])->name('accounts.account-summary.destroy');

    Route::get('/leads/{lead}/payments', [PaymentController::class, 'show'])->name('leads.payments.index');

    Route::post('/api/accounts/{lead}/add-payment', [PaymentController::class, 'addPayment'])->name('api.accounts.add-payment');

    // Operations
    Route::get('/operations', [OperationController::class, 'index'])->name('operations.index');
    Route::get('/operations/{lead}/booking-file', [OperationController::class, 'bookingFile'])->name('operations.booking-file');
    Route::get('/operations/leads', [OperationController::class, 'operationsLeads'])->name('operations.leads');


    // Department-specific booking files (Ticketing / Visa / Insurance) - Self-authorized in Controller
    Route::get('/ticketing', [OperationController::class, 'ticketingIndex'])->name('ticketing.index');
    Route::get('/ticketing/{lead}/booking-file', [OperationController::class, 'ticketingBookingFile'])->name('ticketing.booking-file');
    Route::get('/ticketing/leads', [OperationController::class, 'ticketingLeads'])->name('ticketing.leads');
    Route::get('/visa', [OperationController::class, 'visaIndex'])->name('visa.index');
    Route::get('/visa/{lead}/booking-file', [OperationController::class, 'visaBookingFile'])->name('visa.booking-file');
    Route::get('/visa/leads', [OperationController::class, 'visaLeads'])->name('visa.leads');
    Route::get('/insurance', [OperationController::class, 'insuranceIndex'])->name('insurance.index');
    Route::get('/insurance/{lead}/booking-file', [OperationController::class, 'insuranceBookingFile'])->name('insurance.booking-file');
    Route::get('/insurance/leads', [OperationController::class, 'insuranceLeads'])->name('insurance.leads');

    Route::post('/leads/{lead}/operations', [OperationController::class, 'store'])->name('leads.operations.store');
    Route::put('/leads/{lead}/operations/{operation}', [OperationController::class, 'update'])->name('leads.operations.update');
    Route::post('/leads/{lead}/operations/{operation}/approve', [OperationController::class, 'approve'])->name('leads.operations.approve');
    Route::post('/leads/{lead}/operations/{operation}/reject', [OperationController::class, 'reject'])->name('leads.operations.reject');

    // Post Sales & Documents
    // Documents routes (general)
    Route::get('/leads/{lead}/documents', [DocumentController::class, 'show'])->name('leads.documents.index');
    Route::get('/leads/{lead}/documents/{document}/download', [DocumentController::class, 'download'])->name('leads.documents.download');

    // Post Sales - Self-authorized or Post Sales permission
    Route::get('/post-sales', [DocumentController::class, 'index'])->name('post-sales.index');
    Route::get('/post-sales/{lead}/booking-file', [DocumentController::class, 'bookingFile'])->name('post-sales.booking-file');
    Route::get('/post-sales/leads', [DocumentController::class, 'postSalesLeads'])->name('post-sales.leads');

    // Document routes used by Post Sales
    Route::post('/leads/{lead}/documents', [DocumentController::class, 'store'])->name('leads.documents.store');
    Route::put('/leads/{lead}/documents/bulk-update', [DocumentController::class, 'bulkUpdate'])->name('leads.documents.bulk-update');
    Route::post('/leads/{lead}/traveller-documents', [TravellerDocumentController::class, 'store'])->name('leads.traveller-documents.store');
    Route::delete('/leads/{lead}/traveller-documents/{travellerDocument}', [TravellerDocumentController::class, 'destroy'])->name('leads.traveller-documents.destroy');

    // Customer Payment routes (Post Sales)
    Route::post('/leads/{lead}/payments', [PaymentController::class, 'store'])->name('leads.payments.store');
    Route::put('/leads/{lead}/payments/{payment}', [PaymentController::class, 'update'])->name('leads.payments.update');
    Route::delete('/leads/{lead}/payments/{payment}', [PaymentController::class, 'destroy'])->name('leads.payments.destroy');

    // Stage Update (Post Sales)
    Route::put('/leads/{lead}/stage', [LeadController::class, 'updateStages'])->name('leads.update-stage');
    Route::put('/leads/{lead}/sales-cost', [LeadController::class, 'updateSalesCost'])->name('leads.update-sales-cost');

    // Booking Component Routes (Destinations, Arrival/Departure, Accommodations, Itineraries)
    Route::post('/leads/{lead}/booking-destinations', [LeadController::class, 'storeBookingDestination'])->name('leads.booking-destinations.store');
    Route::put('/leads/{lead}/booking-destinations/{bookingDestination}', [LeadController::class, 'updateBookingDestination'])->name('leads.booking-destinations.update');
    Route::delete('/leads/{lead}/booking-destinations/{bookingDestination}', [LeadController::class, 'destroyBookingDestination'])->name('leads.booking-destinations.destroy');

    Route::post('/leads/{lead}/booking-arrival-departure', [LeadController::class, 'storeBookingArrivalDeparture'])->name('leads.booking-arrival-departure.store');
    Route::put('/leads/{lead}/booking-arrival-departure/{arrivalDeparture}', [LeadController::class, 'updateBookingArrivalDeparture'])->name('leads.booking-arrival-departure.update');
    Route::delete('/leads/{lead}/booking-arrival-departure/{arrivalDeparture}', [LeadController::class, 'destroyBookingArrivalDeparture'])->name('leads.booking-arrival-departure.destroy');

    Route::post('/leads/{lead}/booking-accommodations', [LeadController::class, 'storeBookingAccommodation'])->name('leads.booking-accommodations.store');
    Route::put('/leads/{lead}/booking-accommodations/{accommodation}', [LeadController::class, 'updateBookingAccommodation'])->name('leads.booking-accommodations.update');
    Route::delete('/leads/{lead}/booking-accommodations/{accommodation}', [LeadController::class, 'destroyBookingAccommodation'])->name('leads.booking-accommodations.destroy');

    Route::post('/leads/{lead}/booking-itineraries', [LeadController::class, 'storeBookingItinerary'])->name('leads.booking-itineraries.store');
    Route::put('/leads/{lead}/booking-itineraries/{itinerary}', [LeadController::class, 'updateBookingItinerary'])->name('leads.booking-itineraries.update');
    Route::delete('/leads/{lead}/booking-itineraries/{itinerary}', [LeadController::class, 'destroyBookingItinerary'])->name('leads.booking-itineraries.destroy');

    Route::middleware('permission:verify documents')->group(function () {
        Route::put('/leads/{lead}/documents/{document}', [DocumentController::class, 'update'])->name('leads.documents.update');
    });
    Route::middleware('permission:delete documents')->group(function () {
        Route::delete('/leads/{lead}/documents/{document}', [DocumentController::class, 'destroy'])->name('leads.documents.destroy');
    });

    // Deliveries
    // Deliveries - Self-authorized or Delivery permission
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/{lead}/booking-file', [DeliveryController::class, 'bookingFile'])->name('deliveries.booking-file');
    Route::get('/deliveries/{lead}/download-voucher', [DeliveryController::class, 'downloadVoucher'])->name('deliveries.download-voucher');
    Route::get('/deliveries/{lead}/download-accommodation-voucher/{accommodation}', [DeliveryController::class, 'downloadAccommodationVoucher'])->name('deliveries.download-accommodation-voucher');
    Route::get('/deliveries/leads', [DeliveryController::class, 'deliveriesLeads'])->name('deliveries.leads');


    Route::get('/leads/{lead}/deliveries', [DeliveryController::class, 'show'])->name('leads.deliveries.index');
    Route::post('/leads/{lead}/deliveries', [DeliveryController::class, 'store'])->name('leads.deliveries.store');

    Route::put('/leads/{lead}/deliveries/{delivery}', [DeliveryController::class, 'update'])->name('leads.deliveries.update');
    Route::post('/leads/{lead}/deliveries/{delivery}/upload', [DeliveryController::class, 'upload'])->name('leads.deliveries.upload');


    // Voucher Routes (Operations can create, Delivery can view/download)

    // Operations: Create vouchers

    Route::post('/leads/{lead}/vouchers/service', [\App\Http\Controllers\VoucherController::class, 'createServiceVoucher'])->name('vouchers.create-service');
    Route::post('/leads/{lead}/vouchers/itinerary', [\App\Http\Controllers\VoucherController::class, 'createItineraryVoucher'])->name('vouchers.create-itinerary');
    Route::post('/leads/{lead}/vouchers/accommodation', [\App\Http\Controllers\VoucherController::class, 'createAccommodationVoucher'])->name('vouchers.create-accommodation');
    Route::get('/leads/{lead}/vouchers/{voucher}', [\App\Http\Controllers\VoucherController::class, 'show'])->name('vouchers.show');
    Route::put('/leads/{lead}/vouchers/{voucher}', [\App\Http\Controllers\VoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('/leads/{lead}/vouchers/{voucher}', [\App\Http\Controllers\VoucherController::class, 'destroy'])->name('vouchers.destroy');


    // Delivery & Operations: View and download vouchers

    Route::get('/leads/{lead}/vouchers', [\App\Http\Controllers\VoucherController::class, 'index'])->name('vouchers.index');
    Route::get('/leads/{lead}/vouchers/{voucher}/download', [\App\Http\Controllers\VoucherController::class, 'downloadVoucher'])->name('vouchers.download');



    // Delivery API Routes
    // Route::prefix('api/delivery')->middleware(['auth:sanctum'])->group(function () {
    //     Route::get('/', [DeliveryController::class, 'apiIndex'])->name('api.delivery.index');
    //     Route::post('/{delivery}/assign', [DeliveryController::class, 'assign'])->middleware('permission:assign deliveries')->name('api.delivery.assign');
    //     Route::put('/{delivery}/status', [DeliveryController::class, 'updateStatus'])->middleware('permission:update deliveries')->name('api.delivery.update-status');
    //     Route::post('/{delivery}/upload-files', [DeliveryController::class, 'uploadFiles'])->middleware('permission:update deliveries')->name('api.delivery.upload-files');
    //     Route::get('/export', [DeliveryController::class, 'export'])->middleware('permission:export reports')->name('api.delivery.export');
    // });


    // Destination API Routes
    Route::get('/api/destinations/{destination}/locations', [DestinationController::class, 'getLocations'])->name('api.destinations.locations');

    // Incentives
    Route::get('/incentives', [IncentiveController::class, 'index'])->name('incentives.index');
    Route::post('/incentives', [IncentiveController::class, 'store'])->name('incentives.store');
    Route::put('/incentives/{incentive}', [IncentiveController::class, 'update'])->name('incentives.update');
    Route::delete('/incentives/{incentive}', [IncentiveController::class, 'destroy'])->name('incentives.destroy');
    Route::post('/incentives/{incentive}/approve', [IncentiveController::class, 'approve'])->name('incentives.approve');
    Route::post('/incentives/{incentive}/mark-paid', [IncentiveController::class, 'markPaid'])->name('incentives.mark-paid');


    // Reports
    Route::middleware('permission:view reports')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/leads', [ReportController::class, 'leads'])->name('reports.leads');
        Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
    });
    Route::middleware('permission:export reports')->group(function () {
        Route::get('/reports/export/leads', [ReportController::class, 'exportLeads'])->name('reports.export.leads');
    });

    // HR -      (Admin and HR only)
    Route::get('/hr/employees', [HRController::class, 'index'])->name('hr.employees.index');
    Route::get('/hr/employees/create', [HRController::class, 'create'])->name('hr.employees.create');
    Route::post('/hr/employees', [HRController::class, 'store'])->name('hr.employees.store');
    Route::get('/hr/employees/{employee}', [HRController::class, 'show'])->name('hr.employees.show');
    Route::get('/hr/employees/{employee}/edit', [HRController::class, 'edit'])->name('hr.employees.edit');
    Route::put('/hr/employees/{employee}', [HRController::class, 'update'])->name('hr.employees.update');
    Route::delete('/hr/employees/{employee}', [HRController::class, 'destroy'])->name('hr.employees.destroy');
});
