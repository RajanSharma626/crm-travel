@extends('layouts.app')

@section('title', 'Leads | Travel Shreval')

@section('content')
    <div class="hk-pg-wrapper pb-0">
        <!-- Page Body -->
        <div class="hk-pg-body py-0">
            <div class="contactapp-wrap">
                <div class="contactapp-content">
                    <div class="contactapp-detail-wrap">
                        <header class="contact-header">
                            <div class="w-100 align-items-center justify-content-between">
                                <div class="d-flex justify-content-between w-100">
                                    <a class="contactapp-title link-dark">
                                        <h1>Leads List</h1>
                                    </a>
                                </div>
                            </div>

                        </header>
                        <div class="contact-body">
                            <div data-simplebar class="nicescroll-bar">
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        @foreach ($errors->all() as $error)
                                            {{ $error . ',' }}
                                        @endforeach
                                    </div>
                                @endif

                                <div class="contact-list-view">
                                    <table class="table table-striped table-bordered w-100 mb-5">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Loan Amount (INR)</th>
                                                <th>Phone</th>
                                                <th>City</th>
                                                <th>Salary(mth)</th>
                                                <th>Created On</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td>#1</td>
                                                <td>
                                                    <div class="media align-items-center">
                                                        <div class="media-body">

                                                            <span class="d-block text-high-em text-primary"></span>

                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-nowrap"></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-nowrap"></td>
                                                <td></td>
                                                <td>

                                                </td>
                                            </tr>
                                            {{-- @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">no records found</td>
                                                </tr>
                                                @endforelse --}}
                                        </tbody>
                                    </table>

                                    <div class="d-flex justify-content-center">
                                        {{-- {{ $leads->appends(request()->query())->links('pagination::bootstrap-5') }} --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- /Page Body -->
        @include('layouts.footer')

    </div>
@endsection
