<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Travel Shravel | CRM Login')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Daterangepicker CSS -->
    <link href="vendors/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

    <!-- Data Table CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />

    <!-- CSS -->
    <link href="{{ asset('dist/css/style.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
    <!-- Wrapper -->
    <div class="hk-wrapper hk-pg-auth" data-footer="simple">
        <!-- Main Content -->
        <div class="hk-pg-wrapper pt-0 pb-xl-0 pb-5">
            <div class="hk-pg-body pt-0 pb-xl-0">
                <!-- Container -->
                <div class="container-xxl">
                    <!-- Row -->
                    <div class="row">
                        <div class="col-sm-10 position-relative mx-auto">
                            <div class="auth-content py-8">
                                <form class="w-100" method="POST" action="{{ route('login.auth') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-5 col-md-7 col-sm-10 mx-auto">
                                            <div class="card card-lg card-border">
                                                <div class="card-body">
                                                    <h4 class="mb-4 text-center">Log in to your account</h4>

                                                    @if (session('error'))
                                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                                    @endif

                                                    <div class="row gx-3">
                                                        <div class="form-group col-lg-12">
                                                            <div class="form-label-group">
                                                                <label>User ID</label>
                                                            </div>
                                                            <input class="form-control" placeholder="User ID"
                                                                name="user_id" type="text"
                                                                value="{{ old('user_id') }}">
                                                            @error('user_id')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group col-lg-12">
                                                            <div class="form-label-group">
                                                                <label>Password</label>
                                                            </div>
                                                            <div class="input-group password-check">
                                                                <span class="input-affix-wrapper">
                                                                    <input class="form-control"
                                                                        placeholder="Enter your password"
                                                                        name="password" type="password">
                                                                    <a href="#" class="input-suffix text-muted">
                                                                        <span class="feather-icon"><i class="form-icon"
                                                                                data-feather="eye"></i></span>
                                                                        <span class="feather-icon d-none"><i
                                                                                class="form-icon"
                                                                                data-feather="eye-off"></i></span>
                                                                    </a>
                                                                </span>
                                                            </div>
                                                            @error('password')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group col-lg-12">
                                                            <button type="submit" class="btn btn-primary w-100">
                                                                <span class="btn-text">Sign In</span>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-center">
                                                        <div class="form-check form-check-sm mb-3">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="logged_in" name="remember" checked>
                                                            <label class="form-check-label text-muted fs-7"
                                                                for="logged_in">Keep me logged in</label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- /Row -->
                </div>
                <!-- /Container -->
            </div>
            <!-- /Page Body -->

            <!-- Page Footer -->
            <div class="hk-footer border-0">
                <footer class="container-xxl footer">
                    <div class="row">
                        <div class="col-xl-8 text-center">
                            <p class="footer-text pb-0">
                                <span class="copy-text">© {{ date('Y') }} All rights reserved.</span>
                                <span class="footer-link-sep">|</span>
                                <span>Designed & Developed By </span>
                                <a href="https://www.fiverr.com/rajansharma626" target="_blank">Rajan Sharma</a>
                            </p>
                        </div>
                </footer>
            </div>
            <!-- / Page Footer -->

        </div>
        <!-- /Main Content -->
    </div>
    <!-- /Wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <!-- FeatherIcons JS -->
    <script src="{{ asset('dist/js/feather.min.js') }}"></script>

    <!-- Fancy Dropdown JS -->
    <script src="{{ asset('dist/js/dropdown-bootstrap-extended.js') }}"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('vendors/simplebar/dist/simplebar.min.js') }}"></script>

    <!-- Data Table JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

    <script>
        $('#EmpTable').DataTable({
            scrollX: true,
            autoWidth: false,
            language: {
                search: "",
                searchPlaceholder: "Search",
                sLengthMenu: "_MENU_items",
                paginate: {
                    next: '', // or '→'
                    previous: '' // or '←'
                }
            },
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('custom-pagination pagination-simple');
            }
        });
    </script>

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
    <script src="{{ asset('dist/js/dashboard-data.js') }}"></script>
</body>

</html>
