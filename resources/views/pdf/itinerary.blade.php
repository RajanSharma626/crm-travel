<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Itinerary - {{ $lead->tsq }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 12px;
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
            margin-bottom: 8px;
            color: #000;
        }

        .tagline {
            font-size: 14px;
            color: #555;
            font-style: italic;
            margin-bottom: 10px;
        }

        .logo {
            max-width: 100px;
            max-height: 100px;
            width: auto;
            height: auto;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 11px;
        }

        table td,
        table th {
            padding: 10px 12px;
            border: 1px solid #333;
            text-align: left;
            vertical-align: top;
        }

        table th {
            background-color: #f5f5f5;
            color: #000000;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
        }

        /* Customer Information Table */
        .info-table {
            margin-bottom: 25px;
            border: 1px solid #000000;
            background-color: #ffffff;
        }

        .info-table td {
            border: 1px solid #000000;
            padding: 8px 10px;
            vertical-align: middle;
        }

        .info-table tr {
            border-bottom: 1px solid #000000;
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        .info-table td:first-child {
            font-weight: bold;
            width: 20%;
            background-color: #f5f5f5;
            font-size: 11px;
            color: #000000;
            border-right: 1px solid #000000;
            text-align: left;
            padding: 8px 12px;
        }

        .info-table td:nth-child(2),
        .info-table td:nth-child(3) {
            background-color: #ffffff !important;
        }

        .info-table td:nth-child(4),
        .info-table td:nth-child(6) {
            font-weight: bold;
            text-align: center;
            background-color: #f5f5f5 !important;
            color: #000000;
            font-size: 11px;
            width: 12%;
            border-left: 1px solid #000000;
            letter-spacing: 0.3px;
            white-space: nowrap;
            padding: 8px 6px;
        }

        .info-table td:nth-child(4) strong,
        .info-table td:nth-child(6) strong {
            background-color: transparent;
        }

        .info-table td:nth-child(5),
        .info-table td:nth-child(7) {
            text-align: center;
            background-color: #ffffff !important;
            font-size: 11px;
            color: #000000;
            width: 10%;
            padding: 8px 8px;
        }

        /* Ensure all value cells have white background */
        .info-table td:nth-child(5),
        .info-table td:nth-child(7),
        .info-table .data-cell,
        .info-table .room-types,
        .info-table .voucher-number {
            background-color: #ffffff !important;
        }

        .info-table .data-cell {
            font-weight: 600;
            font-size: 10px !important;
            color: #000;
            line-height: 1.4;
            background-color: #ffffff !important;
        }

        .room-types {
            text-align: center;
            font-size: 11px !important;
            color: #000000;
            background-color: #ffffff !important;
        }

        .room-label {
            font-weight: bold;
            text-align: center;
            background-color: #f5f5f5 !important;
            color: #000000;
            border-right: 1px solid #000000;
            letter-spacing: 0.3px;
            white-space: nowrap;
            padding: 8px 6px;
            font-size: 10px !important;
        }

        .customer-name {
            font-weight: 600;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
            background-color: #ffffff !important;
        }

        .customer-name .name-part {
            font-weight: 600;
            font-size: 12px;
            color: #000;
        }

        .customer-name .count-part {
            font-weight: 500;
            font-size: 11px;
            color: #555;
            margin-left: 5px;
        }

        .contact-info {
            font-weight: 600;
            font-size: 11px;
            color: #000;
            word-break: keep-all;
            white-space: normal;
            line-height: 1.5;
            background-color: #ffffff !important;
        }

        .voucher-label {
            font-weight: bold;
            font-size: 10px;
            white-space: nowrap;
        }

        .voucher-number {
            font-weight: 600;
            font-size: 11px;
            color: #000;
            font-family: 'Courier New', monospace;
            background-color: #ffffff !important;
        }

        .email-cell {
            font-weight: 600;
            font-size: 9px !important;
            color: #000;
            word-break: keep-all;
            white-space: nowrap;
            background-color: #ffffff !important;
        }

        /* Hotel Table Styles */
        .hotel-table {
            margin-bottom: 25px;
            border: 1px solid #000000;
        }

        .hotel-table th {
            background-color: #f5f5f5;
            color: #000000;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            padding: 8px 6px;
            border: 1px solid #000000;
        }

        .hotel-table td {
            padding: 8px 6px;
            font-size: 11px;
            border: 1px solid #000000;
            background-color: #ffffff;
            text-align: center !important;
        }

        .hotel-table td strong {
            font-weight: normal;
        }

        /* Itinerary Table Styles */
        .itinerary-table {
            margin-top: 0;
            border: 1px solid #000000;
        }

        .itinerary-table>thead>tr>th {
            background-color: #f5f5f5;
            color: #000000;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            padding: 8px 6px;
            border: 1px solid #000000;
        }

        .itinerary-table td {
            border: 1px solid #000000;
            vertical-align: top;
        }

        .day-cell {
            font-weight: bold;
            font-size: 12px;
            background-color: #f5f5f5;
            width: 15%;
            padding: 12px 10px;
        }

        .day-number {
            font-size: 14px;
            color: #000000;
        }

        .day-date {
            font-size: 12px;
            color: #555;
            margin-top: 5px;
        }

        .schedule-cell {
            font-size: 12px;
            line-height: 1.8;
        }

        .time-label {
            font-weight: bold;
            font-size: 13px;
            color: #2c3e50;
            display: block;
        }

        .schedule-content {
            font-size: 12px;
            line-height: 1.7;
            margin-left: 0;
        }

        .schedule-content strong {
            font-weight: 600;
        }

        .stay-cell {
            font-size: 12px;
            color: #000;
            background-color: #ffffff;
            padding: 6px 8px !important;
            margin: 0;
        }

        .stay-cell strong {
            font-weight: 600;
            font-style: normal;
        }

        .stay-row-first-cell {
            padding: 6px 8px !important;
            margin: 0;
            font-size: 12px;
        }

        /* Meeting Point Notice */
        .meeting-notice {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 12px 15px;
            font-size: 11px;
            font-weight: 600;
            color: #856404;
            font-style: italic;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding-top: 20px;
            font-style: italic;
            font-size: 13px;
            color: #555;
            font-weight: 500;
        }

        /* Section Spacing */
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 0;
            margin-top: 0;
            color: #000000;
            padding-bottom: 8px;
            text-transform: uppercase;
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

        /* Guest Details Tables */
        .guest-details-wrapper {
            display: table;
            width: 100%;
            table-layout: fixed;
            border: none;
            margin-bottom: 0;
            border-spacing: 0;
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
            margin-bottom: 0;
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
            font-size: 11px;
            color: black;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            text-align: left;
            line-height: 1.4;
        }

        .trpl-note-outside strong {
            font-weight: bold;
            font-style: normal;
        }

        /* Better spacing for activities */
        .activity-item {
            margin-bottom: 6px;
            padding-left: 0;
        }

        .activity-item:before {
            content: "• ";
            font-weight: bold;
            color: #2c3e50;
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-wrapper">
            @if (!isset($withCompanyDetails) || $withCompanyDetails == '1')
                <div class="header-left">
                    <div class="company-name">TRAVEL SHRAVEL</div>
                    <div class="tagline">Nothing Unexplored</div>
                </div>
                @if (isset($logoBase64) && $logoBase64)
                    <div class="header-right">
                        <img src="{{ $logoBase64 }}" alt="Travel Shravel Logo" class="logo">
                    </div>
                @endif
            @else
                {{-- Spacer for layout stability if needed, otherwise empty --}}
                <div class="header-left">&nbsp;</div>
            @endif
        </div>
    </div>

    @php
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
                                <td class="value-cell">
                                    {{ $voucher->voucher_number ?? ($lead->operation->voucher_number ?? 'N/A') }}
                                </td>
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
                                    {{ $voucher->emergency_contact_number ?? '+91 9796614307' }}
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

            At meeting point, there would be a placard holder carrying a placard of your name or company's name.

        </div>
    </div>

    <!-- Hotel Information Table -->
    @if ($lead->bookingAccommodations && $lead->bookingAccommodations->count() > 0)
        <div class="section-title">Accommodation Details</div>
        <table class="hotel-table">
            <thead>
                <tr>
                    <th>Stay at</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Room type</th>
                    <th>No. of rooms</th>
                    <th>Confirmation No.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lead->bookingAccommodations as $accommodation)
                    <tr>
                        <td>{{ $accommodation->stay_at ?? 'N/A' }}</td>
                        <td>{{ $accommodation->checkin_date ? \Carbon\Carbon::parse($accommodation->checkin_date)->format('d-M-y') : 'N/A' }}
                        </td>
                        <td>{{ $accommodation->checkout_date ? \Carbon\Carbon::parse($accommodation->checkout_date)->format('d-M-y') : 'N/A' }}
                        </td>
                        <td>{{ $accommodation->room_type ?? 'N/A' }}</td>
                        <td style="text-align: center;">1</td>
                        <td>{{ $accommodation->confirmation_no ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Itinerary Section -->
    <div class="section-title">Tentative Itinerary</div>
    <table class="itinerary-table">
        <thead>
            <tr>
                <th style="width: 15%;">Day & Date</th>
                <th style="width: 85%;">Schedule & Activities</th>
            </tr>
        </thead>
        <tbody>
            @if ($lead->bookingItineraries && $lead->bookingItineraries->count() > 0)
                @php
                    $groupedItineraries = [];
                    $dayCounter = 1;

                    // Group itineraries by day
                    foreach ($lead->bookingItineraries as $itinerary) {
                        // Extract day number from day_and_date
                        $dayMatch = preg_match('/Day\s*(\d+)/i', $itinerary->day_and_date ?? '', $matches);
                        $dayNumber = $dayMatch ? (int) $matches[1] : $dayCounter;

                        if (!isset($groupedItineraries[$dayNumber])) {
                            $groupedItineraries[$dayNumber] = [];
                        }
                        $groupedItineraries[$dayNumber][] = $itinerary;

                        if (!$dayMatch) {
                            $dayCounter++;
                        }
                    }

                    // Sort by day number
                    ksort($groupedItineraries);
                @endphp

                @foreach ($groupedItineraries as $dayNumber => $dayItineraries)
                    @php
                        $firstItinerary = $dayItineraries[0];

                        // Extract date from day_and_date (use first entry's date if present)
$dateMatch = preg_match(
    '/(\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4})/',
    $firstItinerary->day_and_date ?? '',
    $dateMatches,
);

$dateStr = $dateMatch ? $dateMatches[1] : '';
$formattedDate = '';
if ($dateStr) {
    try {
        $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', str_replace('-', '/', $dateStr));
        $formattedDate = $dateObj->format('d-M-y');
    } catch (\Exception $e) {
        try {
            $dateObj = \Carbon\Carbon::parse($dateStr);
            $formattedDate = $dateObj->format('d-M-y');
        } catch (\Exception $e2) {
            $formattedDate = $dateStr;
        }
    }
}

// Build time labels for all entries of the day (unique)
$timeLabels = [];
foreach ($dayItineraries as $dit) {
    $tStr = $dit->time ? \Carbon\Carbon::parse($dit->time)->format('H:i') : '';
    $tLabel = 'Schedule';
    if (
        stripos($dit->day_and_date ?? '', 'depart') !== false ||
        stripos($dit->day_and_date ?? '', 'departure') !== false
    ) {
        $tLabel = 'Depart';
    } elseif (
        stripos($dit->day_and_date ?? '', 'pick-up') !== false ||
        stripos($dit->day_and_date ?? '', 'pickup') !== false
    ) {
        $tLabel = 'Pick-up';
    } elseif ($tStr) {
        $tLabel = $tStr . ' Hours';
                            }
                            if (!in_array($tLabel, $timeLabels)) {
                                $timeLabels[] = $tLabel;
                            }
                        }
                    @endphp

                    <tr>
                        <td class="day-cell">
                            <div class="day-number">Day {{ $dayNumber }}</div>
                            @foreach ($timeLabels as $tl)
                                <div class="time-label">{{ $tl }}</div>
                            @endforeach
                        </td>
                        <td class="schedule-cell">
                            <div class="schedule-content">
                                @foreach ($dayItineraries as $itinerary)
                                    <div style="margin-bottom:8px;">
                                        @if ($itinerary->location)
                                            <strong>{{ $itinerary->location }}</strong><br>
                                        @endif
                                        @if ($itinerary->activity_tour_description)
                                            @php
                                                $activities = array_filter(
                                                    array_map(
                                                        'trim',
                                                        explode(
                                                            "\n",
                                                            str_replace(
                                                                ["\r\n", "\r"],
                                                                "\n",
                                                                $itinerary->activity_tour_description,
                                                            ),
                                                        ),
                                                    ),
                                                );
                                            @endphp
                                            @foreach ($activities as $activity)
                                                @if (!empty($activity))
                                                    <div class="activity-item">{{ $activity }}</div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="stay-row-first-cell">
                            <strong>Stay:</strong>
                        </td>
                        <td class="stay-cell">
                            Stay overnight at pre-booked accommodation.
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="2" style="text-align: center; padding: 30px; font-size: 13px; color: #999;">
                        <strong>No itinerary data available</strong>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        @if (!isset($withCompanyDetails) || $withCompanyDetails == '1')
            <strong>"Travel Shravel wishes you a very happy, comfortable and safe journey"</strong>
        @else
            <strong>"Wishing you a very happy, comfortable and safe journey"</strong>
        @endif
    </div>
</body>

</html>
