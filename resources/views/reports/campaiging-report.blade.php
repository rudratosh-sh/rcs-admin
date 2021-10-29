@extends('layouts.main') 
@section('title', 'Data Tables')
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


    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-inbox bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Campaiging Report')}}</h5>
                            <span>{{ __('View your campaiging result of current 3 Days')}}</span>
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
                            <li class="breadcrumb-item active" aria-current="page">Campaiging Report</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>


        

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-block">
                        <h3>{{ __('Campaiging Report')}}</h3>
                    </div>
                    <div class="card-body">
                        <div class="dt-responsive">
                            <table id="alt-pg-dt"
                                   class="table table-striped table-bordered nowrap">
                                <thead>
                                <tr>
                                    <th>{{ __('S.N.')}}</th>
                                    <th>{{ __('Submitted Time')}}</th>
                                    <th>{{ __('Scheduled Time')}}</th>
                                    <th>{{ __('SMS Route')}}</th>
                                    <th>{{ __('SMS Text')}}</th>
                                    <th>{{ __('Sent Via')}}</th>
                                    <th>{{ __('No. of SMS')}}</th>
                                    <th>{{ __('Charges')}}</th>
                                    <th>{{ __('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ __(1)}}</td>
                                    <td>{{ __('05:30 PM')}}</td>
                                    <td>{{ __('06:40 PM')}}</td>
                                    <td>{{ __('Some Route')}}</td>
                                    <td>{{ __('Some Message Text')}}</td>
                                    <td>{{ __('Sent Via any Medium')}}</td>
                                    <td>{{ __(4000)}}</td>
                                    <td>{{ __('43 INR')}}</td>
                                    <td>{{ __('Some Action')}}</td>
                                </tr>
                               
                            </tbody>
                                <tfoot>
                                <tr>
                                    <th>{{ __('S.N.')}}</th>
                                    <th>{{ __('Submitted Time')}}</th>
                                    <th>{{ __('Scheduled Time')}}</th>
                                    <th>{{ __('SMS Route')}}</th>
                                    <th>{{ __('SMS Text')}}</th>
                                    <th>{{ __('Sent Via')}}</th>
                                    <th>{{ __('No. of SMS')}}</th>
                                    <th>{{ __('Charges')}}</th>
                                    <th>{{ __('Action')}}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Language - Comma Decimal Place table end -->
            </div>
        </div>
    </div>
               

    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
        <script src="{{ asset('js/datatables.js') }}"></script>
        <script src="{{ asset('plugins/jquery-toast-plugin/dist/jquery.toast.min.js')}}"></script>
        <script src="{{ asset('js/alerts.js')}}"></script>
    @endpush
@endsection
      
