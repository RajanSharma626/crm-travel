<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CRM App')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
    <link rel="shortcut icon" href="/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="CRM" />
    <link rel="manifest" href="/favicon/site.webmanifest" />

    <!-- Daterangepicker CSS -->
    <link href="{{ asset('vendors/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />

    <!-- Data Table CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"
        type="text/css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">


    <!-- CSS -->
    <link href="{{ asset('dist/css/style.css') }}" rel="stylesheet" type="text/css">

    <!-- Page Styles -->
    @stack('styles')
</head>

<body>
    <!-- Wrapper -->
    <div class="hk-wrapper" data-layout="vertical" data-layout-style="collapsed" data-hover="active" data-menu="light"
        data-footer="simple">
        <!-- Top Navbar -->
        <nav class="hk-navbar navbar navbar-expand-xl navbar-light fixed-top">
            <div class="container-fluid">
                <!-- Start Nav -->
                <div class="nav-start-wrap">
                    <button
                        class="btn btn-icon btn-rounded btn-flush-dark flush-soft-hover navbar-toggle d-xl-none"><span
                            class="icon"><span class="feather-icon"><i
                                    data-feather="align-left"></i></span></span></button>
                </div>
                <!-- /Start Nav -->

                <!-- End Nav -->
                <div class="nav-end-wrap">
                    <ul class="navbar-nav flex-row">

                        <li class="nav-item">
                            <div class="dropdown ps-2">
                                <a class="dropdown-toggle no-caret d-flex align-items-center" href="#"
                                    role="button" data-bs-display="static" data-bs-toggle="dropdown"
                                    data-dropdown-animation data-bs-auto-close="outside" aria-expanded="false">
                                    <div class="avatar avatar-rounded rounded-circle avatar-xs me-2"
                                        style="background-color: #007d88;">
                                        <span
                                            class="initial-wrap text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-1 rounded-3">
                                    <div class="dropdown-header">
                                        <h6 class="fw-bold mb-0">{{ Auth::user()->name }}</h6>
                                        <small class="text-muted">ID:
                                            {{ Auth::user()->user_id ?? (Auth::user()->login_work_email ?? 'N/A') }}</small>
                                    </div>
                                    <div class="dropdown-divider"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="dropdown-item d-flex align-items-center text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- /End Nav -->
            </div>
        </nav>
        <!-- /Top Navbar -->

        <!-- Vertical Nav -->
        <div class="hk-menu">
            <!-- Brand -->
            <div class="menu-header">
                <span>
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        <img src="{{ asset('dist/img/transparency Royal Blue 2-01.png') }}" alt="Travel Shravel"
                            style="height: 40px; width: auto;">
                    </a>
                    <button class="btn btn-icon btn-rounded btn-flush-dark flush-soft-hover navbar-toggle">
                        <span class="icon">
                            <span class="svg-icon fs-5">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-arrow-bar-to-left" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="10" y1="12" x2="20" y2="12"></line>
                                    <line x1="10" y1="12" x2="14" y2="16"></line>
                                    <line x1="10" y1="12" x2="14" y2="8"></line>
                                    <line x1="4" y1="4" x2="4" y2="20"></line>
                                </svg>
                            </span>
                        </span>
                    </button>
                </span>
            </div>
            <!-- /Brand -->

            <!-- Main Menu -->
            <div data-simplebar class="nicescroll-bar">
                <div class="menu-content-wrap">
                    <div class="menu-group">
                        <ul class="navbar-nav flex-column">

                            <!-- Dashboard -->
                            <li
                                class="nav-item mb-2 {{ Route::currentRouteName() == 'home' || Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <span class="nav-icon-wrap">
                                        <span class="svg-icon">
                                            <i data-feather="home" class="small"></i>
                                        </span>
                                    </span>
                                    <span class="nav-link-text">Dashboard</span>
                                </a>
                            </li>

                            @php
                                $isAdmin =
                                    Auth::user()->hasRole('Admin') ||
                                    Auth::user()->hasRole('Developer') ||
                                    Auth::user()->department === 'Admin';
                                $isCustomerCare =
                                    Auth::user()->hasRole('Customer Care') ||
                                    Auth::user()->department === 'Customer Care';
                            @endphp

                            <!-- Customer Care - Separate Tab -->
                            @if ($isAdmin || $isCustomerCare)
                                <li class="nav-item mb-2 {{ request()->routeIs('customer-care.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('customer-care.leads.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon">
                                                <i data-feather="headphones" class="small"></i>
                                            </span>
                                        </span>
                                        <span class="nav-link-text">Customer Care</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Sales Tab - Visible to Admin, Sales, Sales Manager (Hidden for Customer Care) -->
                            @if (
                                ($isAdmin ||
                                    Auth::user()->hasRole('Sales') ||
                                    Auth::user()->hasRole('Sales Manager') ||
                                    Auth::user()->department === 'Sales') &&
                                    !$isCustomerCare)
                                @php
                                    $isSalesActive = request()->is('leads*') || request()->is('bookings*');
                                    $isLeadsActive = request()->is('leads*');
                                    $isBookingsActive = request()->is('bookings*');
                                @endphp
                                <li class="nav-item {{ $isSalesActive ? 'active' : '' }}">
                                    <a class="nav-link" href="javascript:void(0);" data-bs-toggle="collapse"
                                        data-bs-target="#dash_chat"
                                        aria-expanded="{{ $isSalesActive ? 'true' : 'false' }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon">
                                                <i data-feather="users" class="small"></i>
                                            </span>
                                        </span>
                                        <span class="nav-link-text">Sales</span>
                                    </a>
                                    <ul id="dash_chat"
                                        class="nav flex-column nav-children collapse {{ $isSalesActive ? 'show' : '' }}">
                                        <li class="nav-item">
                                            <ul class="nav flex-column">
                                                <li class="nav-item {{ $isLeadsActive ? 'active' : '' }}">
                                                    <a class="nav-link {{ $isLeadsActive ? 'active fw-semibold' : '' }}"
                                                        href="{{ route('leads.index') }}"><span
                                                            class="nav-link-text">Leads</span></a>
                                                </li>
                                                <li class="nav-item {{ $isBookingsActive ? 'active' : '' }}">
                                                    <a class="nav-link {{ $isBookingsActive ? 'active fw-semibold' : '' }}"
                                                        href="{{ route('bookings.index') }}"><span
                                                            class="nav-link-text">Bookings</span></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            @endif

                            <!-- Post Sales Tab - Leads + Booking File -->
                            @if (
                                ($isAdmin ||
                                    Auth::user()->hasRole('Post Sales') ||
                                    Auth::user()->hasRole('Post Sales Manager') ||
                                    Auth::user()->department === 'Post Sales') &&
                                    !$isCustomerCare)
                                @php
                                    $isPostActive = request()->is('post-sales*');
                                @endphp
                                <li class="nav-item {{ $isPostActive ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('post-sales.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon"><i data-feather="check-circle"
                                                    class="small"></i></span>
                                        </span>
                                        <span class="nav-link-text">Post Sales</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Operations Tab - Visible to Admin and Operation department only (Hidden for Customer Care) -->
                            @php
                                $isOpsDept =
                                    $isAdmin ||
                                    Auth::user()->hasRole('Operation') ||
                                    Auth::user()->hasRole('Operation Manager') ||
                                    Auth::user()->department === 'Operation';
                                $isOpsActive = request()->is('operations*');
                            @endphp
                            @if ($isOpsDept && !$isCustomerCare)
                                <li class="nav-item {{ $isOpsActive ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('operations.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon"><i data-feather="clipboard"
                                                    class="small"></i></span>
                                        </span>
                                        <span class="nav-link-text">Operations</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Ticketing Tab -->
                            @if (($isAdmin || Auth::user()->hasRole('Ticketing') || Auth::user()->department === 'Ticketing') && !$isCustomerCare)
                                @php
                                    $isTicketActive = request()->is('ticketing*');
                                @endphp
                                <li class="nav-item {{ $isTicketActive ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('ticketing.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon"><i data-feather="airplay"
                                                    class="small"></i></span>
                                        </span>
                                        <span class="nav-link-text">Ticketing</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Visa Tab -->
                            @if (($isAdmin || Auth::user()->hasRole('Visa') || Auth::user()->department === 'Visa') && !$isCustomerCare)
                                @php
                                    $isVisaActive = request()->is('visa*');
                                @endphp
                                <li class="nav-item {{ $isVisaActive ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('visa.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon"><i data-feather="file-text"
                                                    class="small"></i></span>
                                        </span>
                                        <span class="nav-link-text">Visa</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Insurance Tab -->
                            @if (($isAdmin || Auth::user()->hasRole('Insurance') || Auth::user()->department === 'Insurance') && !$isCustomerCare)
                                @php
                                    $isInsActive = request()->is('insurance*');
                                @endphp
                                <li class="nav-item {{ $isInsActive ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('insurance.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon"><i data-feather="shield"
                                                    class="small"></i></span>
                                        </span>
                                        <span class="nav-link-text">Insurance</span>
                                    </a>
                                </li>
                            @endif



                            <!-- Accounts Tab - Visible to Admin, Accounts, Accounts Manager (Hidden for Customer Care) -->
                            @if (
                                ($isAdmin ||
                                    Auth::user()->hasRole('Accounts') ||
                                    Auth::user()->hasRole('Accounts Manager') ||
                                    Auth::user()->department === 'Accounts') &&
                                    !$isCustomerCare)
                                @php
                                    $isAccActive = request()->is('accounts*');
                                @endphp
                                <li class="nav-item {{ $isAccActive ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('accounts.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon"><i data-feather="credit-card"
                                                    class="small"></i></span>
                                        </span>
                                        <span class="nav-link-text">Accounts</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Delivery Tab - Visible to Admin, Delivery, Delivery Manager (Hidden for Customer Care) -->
                            @if (
                                ($isAdmin ||
                                    Auth::user()->hasRole('Delivery') ||
                                    Auth::user()->hasRole('Delivery Manager') ||
                                    Auth::user()->department === 'Delivery') &&
                                    !$isCustomerCare)
                                @php
                                    $isDelActive = request()->is('deliveries*');
                                @endphp
                                <li class="nav-item {{ $isDelActive ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('deliveries.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon"><i data-feather="truck" class="small"></i></span>
                                        </span>
                                        <span class="nav-link-text">Delivery</span>
                                    </a>
                                </li>
                            @endif

                            <!-- HR Tab - Visible to Admin and HR only (Hidden for Customer Care) -->
                            @if (($isAdmin || Auth::user()->hasRole('HR') || Auth::user()->department === 'HR') && !$isCustomerCare)
                                <li class="nav-item mb-2 {{ request()->is('hr*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('hr.employees.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon">
                                                <i data-feather="users" class="small"></i>
                                            </span>
                                        </span>
                                        <span class="nav-link-text">HR</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Services - Admin and Operations only -->
                            @php
                                $canAccessServices = $isAdmin || 
                                    Auth::user()->department === 'Operation' || 
                                    Auth::user()->department === 'Operations' ||
                                    Auth::user()->hasRole('Operation') ||
                                    Auth::user()->hasRole('Operations');
                            @endphp
                            @if($canAccessServices)
                                <li class="nav-item mb-2 {{ request()->is('services*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('services.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon">
                                                <i data-feather="briefcase" class="small"></i>
                                            </span>
                                        </span>
                                        <span class="nav-link-text">Services</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Destinations - Admin and Operations only -->
                            @if($canAccessServices)
                                <li class="nav-item mb-2 {{ request()->is('destinations*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('destinations.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon">
                                                <i data-feather="map-pin" class="small"></i>
                                            </span>
                                        </span>
                                        <span class="nav-link-text">Destinations</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Hotels - Admin and Operations only -->
                            @if($canAccessServices)
                                <li class="nav-item mb-2 {{ request()->is('hotels*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('hotels.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon">
                                                <i data-feather="home" class="small"></i>
                                            </span>
                                        </span>
                                        <span class="nav-link-text">Hotels</span>
                                    </a>
                                </li>
                            @endif


                            <!-- Incentives Tab - Visible to Admin and HR only (Hidden for Customer Care) -->
                            @if (($isAdmin || Auth::user()->hasRole('HR') || Auth::user()->department === 'HR') && !$isCustomerCare)
                                <li class="nav-item mb-2 {{ request()->is('incentives*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('incentives.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon">
                                                <i data-feather="dollar-sign" class="small"></i>
                                            </span>
                                        </span>
                                        <span class="nav-link-text">Incentives</span>
                                    </a>
                                </li>
                            @endif


                            <!-- Reports -->
                            {{-- <li class="nav-item mb-2 {{ request()->is('reports*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('reports.index') }}">
                                    <span class="nav-icon-wrap">
                                        <span class="svg-icon">
                                            <i data-feather="bar-chart-2" class="small"></i>
                                        </span>
                                    </span>
                                    <span class="nav-link-text">Reports</span>
                                </a>
                            </li> --}}

                            <!-- Users removed - Now using HR tab to create user profiles and login details -->
                            {{-- @can('view users')
                                <li class="nav-item mb-2 {{ request()->is('users*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('users') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon">
                                                <i data-feather="user" class="small"></i>
                                            </span>
                                        </span>
                                        <span class="nav-link-text">Users</span>
                                    </a>
                                </li>
                            @endcan --}}

                            <!-- Settings -->
                            {{-- <li class="nav-item mb-2 {{ request()->is('settings*') ? 'active' : '' }}">
                                <a class="nav-link" href="#">
                                    <span class="nav-icon-wrap">
                                        <span class="svg-icon">
                                            <i data-feather="settings" class="small"></i>
                                        </span>
                                    </span>
                                    <span class="nav-link-text">Settings</span>
                                </a>
                            </li> --}}

                            <!-- Incentive Rules (Admin only) -->
                            {{-- @can('view incentive rules')
                                <li class="nav-item mb-2 {{ request()->is('incentive-rules*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('incentive-rules.index') }}">
                                        <span class="nav-icon-wrap">
                                            <span class="svg-icon">
                                                <i data-feather="file-text" class="small"></i>
                                            </span>
                                        </span>
                                        <span class="nav-link-text">Incentive Rules</span>
                                    </a>
                                </li>
                            @endcan --}}

                        </ul>
                    </div>
                </div>
            </div>

            <!-- /Main Menu -->
        </div>
        <div id="hk_menu_backdrop" class="hk-menu-backdrop"></div>
        <!-- /Vertical Nav -->


        <!-- Main Content -->
        @yield('content')
        <!-- /Main Content -->
    </div>
    <!-- /Wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
    </script>

    </script>

    {{-- Users JavaScript removed - Now using HR tab to create user profiles and login details --}}
    {{-- <script>
        $(document).ready(function() {
            $('.edit-users-btn').on('click', function() {
                let empId = $(this).data('emp-id');

                $.ajax({
                    url: '/user/edit/' + empId,
                    type: 'GET',
                    success: function(data) {
                        // Populate modal fields
                        $('#emp_name').val(data.name);
                        $('#emp_email').val(data.email);
                        $('#emp_position').val(data.role);
                        $('#emp_status').val(data.status);
                        $('#emp_password').val(''); // Optional: empty for security

                        // Optionally store users ID for update
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'emp_id',
                            name: 'id',
                            value: data.id
                        }).appendTo('form');
                    },
                    error: function() {
                        alert('Failed to fetch users data.');
                    }
                });
            });
        });
    </script> --}}

    <script>
        $(document).ready(function() {
            $('.assignAgentModal').on('click', function(e) {
                e.preventDefault();

                // Get leadId from data attribute
                var leadId = $(this).data('leadid');

                // Set value in modal input/display
                $('#lead_id').val(leadId);

                // Show the modal
                var modal = new bootstrap.Modal(document.getElementById('assignAgentModal'));
                modal.show();
            });
        });
    </script>



    <!-- FeatherIcons JS -->
    <script src="{{ asset('dist/js/feather.min.js') }}"></script>

    <!-- Fancy Dropdown JS -->
    <script src="{{ asset('dist/js/dropdown-bootstrap-extended.js') }}"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('vendors/simplebar/dist/simplebar.min.js') }}"></script>

    <!-- Data Table JS -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> --}}

    <!-- Daterangepicker JS -->
    <script src="{{ asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('vendors/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('dist/js/daterangepicker-data.js') }}"></script>

    <!-- Amcharts Maps JS -->
    {{-- <script src="../../../../cdn.amcharts.com/lib/5/index.js"></script>
    <script src="../../../../cdn.amcharts.com/lib/5/map.js"></script>
    <script src="../../../../cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
    <script src="../../../../cdn.amcharts.com/lib/5/themes/Animated.js"></script> --}}

    <!-- Apex JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Init JS -->
    <script src="{{ asset('dist/js/init.js') }}"></script>
    <script src="{{ asset('dist/js/chips-init.js') }}"></script>
    {{-- <script src="{{ asset('dist/js/dashboard-data.js') }}"></script> --}}

    <!-- Sidebar Menu Logic -->
    <script>
        $(document).ready(function() {
            // Keep submenu open if child is active on page load
            $('.nav-item.active').each(function() {
                const parentSubmenu = $(this).closest('.nav-children');
                if (parentSubmenu.length) {
                    parentSubmenu.addClass('show');
                    parentSubmenu.prev('.nav-link[data-bs-toggle="collapse"]').attr('aria-expanded',
                        'true');
                }
            });

            // Add fw-semibold to active submenu items on page load
            $('.nav-children .nav-link.active').addClass('fw-semibold');

            // Handle submenu collapse/expand - close others when opening one
            $('.nav-link[data-bs-toggle="collapse"]').on('click', function(e) {
                const target = $(this).data('bs-target');
                const isExpanded = $(this).attr('aria-expanded') === 'true';

                // Close other submenus
                $('.nav-link[data-bs-toggle="collapse"]').not(this).each(function() {
                    const otherTarget = $(this).data('bs-target');
                    if (otherTarget) {
                        $(otherTarget).collapse('hide');
                        $(this).attr('aria-expanded', 'false');
                    }
                });
            });

            // Update aria-expanded when collapse events fire
            $('.nav-children').on('show.bs.collapse', function() {
                $(this).prev('.nav-link[data-bs-toggle="collapse"]').attr('aria-expanded', 'true');
            });

            $('.nav-children').on('hide.bs.collapse', function() {
                $(this).prev('.nav-link[data-bs-toggle="collapse"]').attr('aria-expanded', 'false');
            });
        });
    </script>

    <!-- Page Scripts -->
    @stack('scripts')
</body>

</html>
