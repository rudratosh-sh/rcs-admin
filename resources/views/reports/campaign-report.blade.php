@extends('layouts.main') 
@section('title', 'campaign Reports')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/jquery-toast-plugin/dist/jquery.toast.min.css')}}">
    @endpush
 
    @if (session('status'))
        <div class="alert alert-success">
            <script>showSuccessToast({{ session('message') }})</script>
            {{ session('message') }}
        </div>
    @endif
    <style>
        td.details-control {
            background: url('../resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }
        tr.details td.details-control {
            background: url('../resources/details_close.png') no-repeat center center;
        }
        th.sorting_disabled.details-control {
            padding-right: 45px;
        }
        .smart-image{
            width: 250px
        }

        img.smart-image {
            width: 250px;
        }


    </style>

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-inbox bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('campaign Report')}}</h5>
                            <span>{{ __('View your campaign result of current 3 Days')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{route('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">Reports</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Campaign Report</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
             <!-- start message area-->
             @include('include.message')
             <!-- end message area-->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>{{ __('Campaign Report')}}</h3></div>
                    <div class="card-body">
                        <table id="campaign_table" class="table table-striped table-bordered nowrap table-responsive" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ __('S.N.')}}</th>
                                    <th>{{ __('Campaign ID')}}</th>
                                    <th>{{ __('Message Format')}}</th>
                                    <th>{{ __('User Id')}}</th>
                                    <th>{{ __('User Email')}}</th>
                                    <th>{{ __('User Name')}}</th>
                                    <th>{{ __('User Mobile')}}</th>
                                    <th>{{ __('Sms Count')}}</th>
                                    <th>{{ __('Sms Failed')}}</th>
                                    <th>{{ __('Sms Success')}}</th>
                                    <th>{{ __('Messages')}}</th>
                                    <th>{{ __('Status')}}</th>
                                    <th>{{ __('Download')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
        <script src="{{ asset('js/datatables.js') }}"></script>
        <script src="{{ asset('plugins/jquery-toast-plugin/dist/jquery.toast.min.js')}}"></script>
        <script src="{{ asset('js/alerts.js')}}"></script>
        <script src="{{ asset('js/custom.js')}}"></script>
    @endpush
@endsection
      
