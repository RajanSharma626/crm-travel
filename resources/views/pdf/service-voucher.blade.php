<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Voucher - {{ $lead->tsq }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #000;
            padding: 20px;
        }

        /* Header Styles */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000000;
        }

        .header-wrapper {
            display: table-row;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 70%;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 30%;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 5px;
            color: #000;
        }

        .voucher-title {
            font-size: 22px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 12px;
            margin-top: 8px;
        }

        .greeting {
            font-size: 12px;
            color: #000;
            margin-top: 5px;
            line-height: 1.8;
        }

        .logo {
            max-width: 100px;
            max-height: 100px;
            width: auto;
            height: auto;
        }

        /* Section Dividers */
        .section-divider {
            margin: 25px 0 15px 0;
        }

        .section-header {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            padding-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
            border: 1px solid #000000;
        }

        table td,
        table th {
            padding: 6px 8px;
            border: 1px solid #000000;
            text-align: left;
            vertical-align: middle;
        }

        table th {
            background-color: #000000;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            padding: 8px;
        }

        /* Accommodation Table */
        .accommodation-table {
            margin-bottom: 25px;
        }

        .accommodation-table td {
            padding: 6px 8px;
            font-size: 11px;
        }

        .accommodation-table td:first-child,
        .accommodation-table td:nth-child(3),
        .accommodation-table td:nth-child(5),
        .accommodation-table td:nth-child(7) {
            font-weight: bold;
            background-color: #f5f5f5;
            /* width: 12%; */
            color: #000000;
            font-size: 11px;
        }

        .accommodation-table td:nth-child(2),
        .accommodation-table td:nth-child(4),
        .accommodation-table td:nth-child(6),
        .accommodation-table td:nth-child(8) {
            background-color: #ffffff;
            font-size: 11px;
        }

        /* Guest Details Tables */
        .guest-details-wrapper {
            width: 100%;
            margin-bottom: 0;
            table-layout: fixed;
            border: none;
        }

        .guest-details-wrapper td {
            vertical-align: top;
            padding: 0;
            border: none;
        }

        .guest-details-wrapper td:first-child {
            width: 50%;
        }

        .guest-details-wrapper td:nth-child(2),
        .guest-details-wrapper td:nth-child(3) {
            width: 25%;
        }

        .guest-details-table {
            width: 100%;
            margin: 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .guest-details-table th {
            background-color: #f5f5f5;
            color: #000000;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            padding: 8px 6px;
            border: 1px solid #000000;
        }

        .guest-details-table td {
            padding: 6px 8px;
            font-size: 11px;
            vertical-align: middle;
            border: 1px solid #000000;
        }

        .guest-details-table .label-cell {
            font-weight: bold;
            background-color: #f5f5f5;
            text-align: left;
            color: #000000;
            font-size: 11px;
            width: 40%;
        }

        .guest-details-table .value-cell {
            text-align: left;
            background-color: #ffffff;
            font-size: 11px;
            width: 60%;
        }

        .guest-details-table .name-cell {
            font-size: 11px;
            background-color: #ffffff;
            text-align: left;
        }

        .trpl-note-outside {
            font-style: italic !important;
            font-size: 10px;
            color: #000000;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 0;
            text-align: left;
            line-height: 1.4;
        }


        /* Flight Table */

        .flight-table th {
            background-color: #f5f5f5;
            color: #000000;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            padding: 8px 6px;
        }

        .flight-table td {
            padding: 8px 6px;
            font-size: 11px;
            text-align: center;
            background-color: #ffffff;
        }

        .flight-table td strong {
            font-weight: bold;
        }

        /* Hotel Contact Table */

        .hotel-contact-table td {
            padding: 8px 10px;
            font-size: 11px;
        }

        .hotel-contact-table td:first-child,
        .hotel-contact-table td:nth-child(3) {
            font-weight: bold;
            background-color: #f5f5f5;
            width: 12%;
            color: #000000;
        }

        .hotel-contact-table td:nth-child(2),
        .hotel-contact-table td:nth-child(4) {
            background-color: #ffffff;
            font-size: 12px;
        }

        /* Services Section */
        .services-section {
            padding: 15px;
            background-color: #ffffff;
            border: 1px solid #000000;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .services-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .services-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            font-size: 11px;
            line-height: 1.6;
            border-bottom: 1px solid #e0e0e0;
        }

        .services-list li:last-child {
            border-bottom: none;
        }

        .services-list li:before {
            content: "•";
            position: absolute;
            left: 8px;
            font-weight: bold;
            color: #000000;
            font-size: 14px;
        }

        /* Comments, Remarks, Caution Sections */
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #ffffff;
            border-left: 2px solid #000000;
            border-right: 1px solid #000000;
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }

        .info-section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000000;
            text-transform: uppercase;
        }

        .info-section-content {
            font-size: 11px;
            line-height: 1.7;
            color: #000;
        }

        /* Footer */
        .footer {
            margin-top: 35px;
            padding: 15px;
            border: 1px solid #000;
            font-size: 10px;
            color: #2c3e50;
        }

        .footer-header {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .footer-title {
            display: table-cell;
            font-style: italic;
            font-size: 10px;
            color: #2c3e50;
            vertical-align: top;
            width: 65%;
        }

        .signature-box {
            display: table-cell;
            vertical-align: top;
            text-align: right;
            width: 35%;
            padding-left: 10px;
        }

        .signature-placeholder {
            width: 140px;
            height: 45px;
            border: 1px solid #2c3e50;
            margin-left: auto;
            margin-right: 0;
            margin-bottom: 5px;
            background-color: #ffffff;
        }

        .signature-label {
            font-size: 9px;
            color: #2c3e50;
            text-align: center;
            width: 140px;
            margin-left: auto;
            margin-right: 0;
        }

        .footer-company-name {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            color: #2c3e50;
            margin-bottom: 12px;
        }

        .footer-address-section {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .address-left {
            display: table-cell;
            vertical-align: top;
            width: 22%;
            font-weight: bold;
            padding-right: 8px;
        }

        .address-right {
            display: table-cell;
            vertical-align: top;
            width: 78%;
        }

        .footer-contact-section {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .contact-left {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .contact-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            text-align: right;
            padding-left: 10px;
        }

        .footer-website {
            text-align: center;
            text-decoration: underline;
            color: #0066cc;
            font-size: 10px;
            margin-top: 8px;
        }

        .footer-content {
            font-size: 10px;
            color: #2c3e50;
        }

        .footer-content .bold {
            font-weight: bold;
        }

        .footer-content div {
            font-size: 11px;
            margin-bottom: 12px;
            color: #2c3e50;
        }

        .footer-content {
            font-size: 10px;
            line-height: 1.8;
        }

        .footer-content div {
            margin-bottom: 5px;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-wrapper">
            <div class="header-left">
                <div class="voucher-title">Service Voucher</div>
                <div class="greeting">
                    Dear Correlate,<br>
                    Kindly provide the mentioned services to our esteemed guests in the exchange of this voucher.
                </div>
            </div>
            @if (isset($logoBase64) && $logoBase64)
                <div class="header-right">
                    <img src="{{ $logoBase64 }}" alt="Travel Shravel Logo" class="logo">
                </div>
            @endif
        </div>
    </div>

    @php
        $accommodations = $lead->bookingAccommodations ?? collect();
        $totalGuests =
            ($lead->adults ?? 0) + ($lead->children_2_5 ?? 0) + ($lead->children_6_11 ?? 0) + ($lead->infants ?? 0);
        $adults = $lead->adults ?? 0;
        $eba = $lead->children_2_5 ?? 0;
        $cwb = $lead->children_6_11 ?? 0;
        $cnb = $lead->children_2_5 ?? 0;
        $infants = $lead->infants ?? 0;

        // Calculate room types
        $dbl = floor($adults / 2);
        $remaining = $adults % 2;
        $trpl = $remaining > 0 || $cwb > 0 ? 1 : 0;
        $quad = 0; // Quad room calculation can be added later if needed
        $totalRooms = $dbl + $trpl + $quad;
    @endphp

    <!-- Accommodation Details Section -->
    @if ($accommodations->count() > 0)
        <div class="section-divider">
            <table class="accommodation-table">
                @foreach ($accommodations as $accommodation)
                    @php
                        $checkinDate = $accommodation->checkin_date ?? null;
                        $checkoutDate = $accommodation->checkout_date ?? null;
                        $nights = 0;
                        if ($checkinDate && $checkoutDate) {
                            $nights = \Carbon\Carbon::parse($checkinDate)->diffInDays(
                                \Carbon\Carbon::parse($checkoutDate),
                            );
                        }
                    @endphp
                    <tr>
                        <td><strong>Stay at</strong></td>
                        <td>{{ $accommodation->stay_at ?? 'N/A' }}</td>
                        <td><strong>Check-in</strong></td>
                        <td>{{ $checkinDate ? \Carbon\Carbon::parse($checkinDate)->format('d-M-y') : 'N/A' }}</td>
                        <td><strong>Check-out</strong></td>
                        <td>{{ $checkoutDate ? \Carbon\Carbon::parse($checkoutDate)->format('d-M-y') : 'N/A' }}</td>
                        <td><strong>Number of Nights</strong></td>
                        <td>{{ $nights }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <!-- Guest Details Section -->
    <div class="section-divider">
        <div class="section-header">Booking Summary</div>
        <table class="guest-details-wrapper">
            <tr>
                <td>
                    <!-- First Table: Guest Information -->
                    <table class="guest-details-table">
                        <thead>
                            <tr>
                                <th colspan="2">Overview</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="label-cell">TST No.</td>
                                <td class="value-cell">{{ $lead->tsq }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Name</td>
                                <td class="name-cell">{{ $lead->salutation ?? '' }} {{ $lead->first_name }}
                                    {{ $lead->last_name ?? '' }} x {{ $totalGuests }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Travel Date</td>
                                <td class="value-cell">
                                    {{ $lead->travel_date ? \Carbon\Carbon::parse($lead->travel_date)->format('d-M-y') : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label-cell">Voucher no.</td>
                                <td class="value-cell">{{ $lead->operation->voucher_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Dated</td>
                                <td class="value-cell">
                                    {{ $lead->travel_date ? \Carbon\Carbon::parse($lead->travel_date)->format('d-M-y') : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label-cell">
                                    Emergency No. :
                                </td>
                                <td class="value-cell">
                                    +91 9796614307
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <!-- Second Table: Guest Count -->
                    <table class="guest-details-table">
                        <thead>
                            <tr>
                                <th colspan="2">Guest Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="label-cell">Adult(s)</td>
                                <td class="value-cell">{{ $adults }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">EBA</td>
                                <td class="value-cell">{{ $eba }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">CWB</td>
                                <td class="value-cell">{{ $cwb }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">CNB</td>
                                <td class="value-cell">{{ $cnb }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">INF</td>
                                <td class="value-cell">{{ $infants }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Total</td>
                                <td class="value-cell">{{ $totalGuests }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <!-- Third Table: Type of Room -->
                    <table class="guest-details-table">
                        <thead>
                            <tr>
                                <th colspan="2">Type of Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="label-cell">SGL</td>
                                <td class="value-cell">0</td>
                            </tr>
                            <tr>
                                <td class="label-cell">DBL</td>
                                <td class="value-cell">{{ $dbl }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">TWIN</td>
                                <td class="value-cell">0</td>
                            </tr>
                            <tr>
                                <td class="label-cell">TRPL*</td>
                                <td class="value-cell">{{ $trpl }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">QUAD</td>
                                <td class="value-cell">{{ $quad }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Total</td>
                                <td class="value-cell">{{ $totalRooms }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <div class="trpl-note-outside">
            <strong>TRPL*</strong> Double bedded room will be provided with a mattress on the floor / roll away bed /
            extra bed / sofa cum bed / depending upon the availability.
        </div>
    </div>

    <!-- Flight/Transport Details Section -->
    @if ($lead->bookingArrivalDepartures && $lead->bookingArrivalDepartures->count() > 0)
        <div class="section-divider">
            <div class="section-header">Arrival/Departure Details</div>
            <table class="flight-table">
                <thead>
                    <tr>
                        <th>Mode</th>
                        <th>Info</th>
                        <th>From City</th>
                        <th>To City</th>
                        <th colspan="2">Dep. Date & Time</th>
                        <th colspan="2">Arrival Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lead->bookingArrivalDepartures as $flight)
                        <tr>
                            <td>{{ $flight->mode ?? 'N/A' }}</td>
                            <td>{{ $flight->info ?? 'N/A' }}</td>
                            <td>{{ $flight->from_city ?? 'N/A' }}</td>
                            <td>{{ $flight->to_city ?? 'N/A' }}</td>
                            <td>
                                @if ($flight->departure_date && $flight->departure_time)
                                    {{ \Carbon\Carbon::parse($flight->departure_date)->format('d-M-y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($flight->departure_date && $flight->departure_time)
                                    {{ \Carbon\Carbon::parse($flight->departure_time)->format('H:i') }} Hrs
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($flight->arrival_date && $flight->arrival_time)
                                    {{ \Carbon\Carbon::parse($flight->arrival_date)->format('d-M-y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($flight->arrival_date && $flight->arrival_time)
                                    {{ \Carbon\Carbon::parse($flight->arrival_time)->format('H:i') }} Hrs
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Hotel Contact Information Section -->
    @if ($accommodations->count() > 0)
        <div class="section-divider">
            <div class="section-header">Hotel Contact Information</div>
            <table class="hotel-contact-table">
                @foreach ($accommodations as $index => $accommodation)
                    @if ($accommodation->stay_at)
                        @if ($index > 0)
                            <tr class="hotel-separator">
                                <td colspan="4" style="padding: 5px; background-color: transparent; border: none;">
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td><strong>Name</strong></td>
                            <td>{{ $accommodation->stay_at }}</td>
                            <td><strong>Call</strong></td>
                            <td>{{ $accommodation->contact_number ?? '+91 145079000' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Address</strong></td>
                            <td colspan="3">{{ $accommodation->location ?? 'N/A' }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    @endif

    <!-- Services Section -->
    <div class="section-divider">
        <div class="section-title">SERVICES TO BE PROVIDED AS PER THE GUEST COUNT:</div>
        <div class="services-section">
            @if (isset($voucher) && $voucher->service_provided)
                {{-- Display dynamic service from voucher --}}
                <div style="white-space: pre-wrap; padding: 10px 15px;">{!! $voucher->service_provided !!}</div>
            @elseif($lead->bookingItineraries && $lead->bookingItineraries->count() > 0)
                {{-- Fallback to itinerary data --}}
                <ul class="services-list">
                    @foreach ($lead->bookingItineraries as $itinerary)
                        @if ($itinerary->activity_tour_description)
                            @php
                                $activities = array_filter(
                                    array_map(
                                        'trim',
                                        explode(
                                            "\n",
                                            str_replace(["\r\n", "\r"], "\n", $itinerary->activity_tour_description),
                                        ),
                                    ),
                                );
                            @endphp
                            @foreach ($activities as $activity)
                                @if (!empty($activity))
                                    <li>{{ $activity }}</li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    <li>Return airport transfers</li>
                    <li>Tour and Transfers on sharing basis</li>
                </ul>
            @else
                <ul class="services-list">
                    <li>Services will be provided as per the itinerary</li>
                    <li>Return airport transfers</li>
                    <li>Tour and Transfers on sharing basis</li>
                </ul>
            @endif
        </div>
    </div>

    <!-- Comments Section -->
    <div class="info-section">
        <div class="info-section-title">COMMENTS</div>
        <div class="info-section-content">
            {{-- Always show default comment first --}}
            All above services are pre-paid. Extras to be collect directly, if any.
            @if (isset($voucher) && $voucher->comments)
                {{-- Show additional comments from voucher after default text --}}
                <br><br>{!! $voucher->comments !!}
            @endif
            @if ($accommodation && $accommodation->confirmation_no)
                <br><br><strong>{{ $accommodation->room_type ?? 'Deluxe Room' }}</strong> in
                <strong>{{ $accommodation->stay_at ?? 'Hotel' }}</strong> is confirmed under confirmation number
                <strong>{{ $accommodation->confirmation_no }}</strong>.
            @endif
        </div>
    </div>

    <!-- Remarks Section -->
    <div class="info-section">
        <div class="info-section-title">Remarks</div>
        <div class="info-section-content">
            For hotel factsheet, exact location, facilities offered and star categorisation of the hotel, we request you
            to log on to the respective hotels website. Early check-in and late check-out is subject to availability.
            Standard check-in time observed by the hotels is 1400 - 1600 hrs and 0900 - 1100 hrs as the check-out time.
            Usually hotels have twin bedded rooms. Double bedded room is subject to availability. Triple room may
            provided with mattress on the floor / roll away bed / extra bed / sofa cum bed / depending upon the
            availability.
        </div>
    </div>

    <!-- Caution Section -->
    <div class="info-section">
        <div class="info-section-title">Caution:</div>
        <div class="info-section-content">
            Heart patients, pregnant women, person suffering from physical problems need to be cautious while visiting
            the following sightseeing: water sports activities, long tour or any stressful activity.
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-header">
            <div class="footer-title">For further details / queries, approach
                <div class="footer-company-name">TRAVEL SHRAVEL TOUR & TRAVELS</div>
            </div>
            {{-- <div class="signature-box">
                <div class="signature-placeholder"></div>
                <div class="signature-label">(Authorised Signatory)</div>
            </div> --}}
        </div>

        <div class="footer-contact-section">
            <div class="contact-left">Email: travel@travelshravel.com</div>
            <div class="contact-right">Mobile: +91 9086421601</div>
        </div>
        <div class="footer-website">www.travelshravel.com</div>
    </div>
</body>

</html>
