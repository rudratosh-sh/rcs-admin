@extends('layouts.main') 
@section('title', 'Dashboard')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush
    <div class="container-fluid">
        <div class="alert alert-primary" role="alert">
            Current User Stats
          </div>
    	<div class="row">
    		<!-- page statustic chart start -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-red text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$basicCountToday->basic_sms_count_all+$advanceCountToday->advance_sms_count_today}}</h4>
                                <p class="mb-0">{{ __('Spend Today')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-activity f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart1" class="chart-line chart-shadow"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-blue text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$basicCountYesterday->basic_sms_count_yesterday+$advanceCountYesterday->advance_sms_count_yesterday}}</h4>
                                <p class="mb-0">{{ __('Spend Yesterday')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-activity f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart2" class="chart-line chart-shadow" ></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-green text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$basicCountWeek->basic_sms_count_week+$advanceCountWeek->advance_sms_count_week}}</h4>
                                <p class="mb-0">{{ __('Spend in Last 7 days')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-activity f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart3" class="chart-line chart-shadow"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-yellow text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$basicCountOverall->basic_sms_count_all+$advanceCountOverall->advance_sms_count_all}}</h4>
                                <p class="mb-0">{{ __('Spend Overall')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-activity f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart4" class="chart-line chart-shadow" ></div>
                    </div>
                </div>
            </div>
    	</div>
    </div>
    @if(Auth::user()->roles->pluck('name')->toArray()[0]=='Super Admin')
    <div class="container-fluid">
        <div class="alert alert-primary" role="alert">
            Overall User Stats
          </div>
    	<div class="row">
    		<!-- page statustic chart start -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-red text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$basicCountTodayAdmin->basic_sms_count_all+$advanceCountTodayAdmin->advance_sms_count_today}}</h4>
                                <p class="mb-0">{{ __('Spend Today')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-activity f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart1" class="chart-line chart-shadow"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-blue text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$basicCountYesterdayAdmin->basic_sms_count_yesterday+$advanceCountYesterdayAdmin->advance_sms_count_yesterday}}</h4>
                                <p class="mb-0">{{ __('Spend Yesterday')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-activity f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart2" class="chart-line chart-shadow" ></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-green text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$basicCountWeekAdmin->basic_sms_count_week+$advanceCountWeekAdmin->advance_sms_count_week}}</h4>
                                <p class="mb-0">{{ __('Spend in Last 7 days')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-activity f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart3" class="chart-line chart-shadow"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-yellow text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$basicCountOverallAdmin->basic_sms_count_all+$advanceCountOverallAdmin->advance_sms_count_all}}</h4>
                                <p class="mb-0">{{ __('Spend Overall')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-activity f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart4" class="chart-line chart-shadow" ></div>
                    </div>
                </div>
            </div>
    	</div>
    </div>
    @endif
	<!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/owl.carousel/dist/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('plugins/chartist/dist/chartist.min.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.js') }}"></script>
        <!-- <script src="{{ asset('plugins/flot-charts/jquery.flot.categories.js') }}"></script> -->
        <script src="{{ asset('plugins/flot-charts/curvedLines.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.tooltip.min.js') }}"></script>

        <script src="{{ asset('plugins/amcharts/amcharts.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/serial.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/themes/light.js') }}"></script>
       
        
        <script src="{{ asset('js/widget-statistic.js') }}"></script>
        <script src="{{ asset('js/widget-data.js') }}"></script>
        <script src="{{ asset('js/dashboard-charts.js') }}"></script>
        
    @endpush
@endsection