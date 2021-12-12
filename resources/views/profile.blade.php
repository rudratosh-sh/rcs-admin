@extends('layouts.main') 
@section('title', 'Profile')
@section('content')
    

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-file-text bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Profile')}}</h5>
                            <span>{{ __('User can update their basic information')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{route('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Profile')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
              <!-- start message area-->
              @include('include.message')
              <!-- end message area-->
            <div class="col-lg-8 col-md-7">
                <div class="card">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="previous-month" role="tabpanel" aria-labelledby="pills-setting-tab">
                            <div class="card-body">
                                <form class="forms-sample" method="POST" action="{{ url('profile/update') }}" >
                                    @csrf
                                        <input type="hidden" name="id" value="{{$user->id}}">
                                        <div class="row">
                                            <div class="col-sm-6">
            
                                                <div class="form-group">
                                                    <label for="name">{{ __('Username')}}<span class="text-red">*</span></label>
                                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ clean($user->name, 'titles')}}" required>
                                                    <div class="help-block with-errors"></div>
            
                                                    @error('name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">{{ __('Email')}}<span class="text-red">*</span></label>
                                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ clean($user->email, 'titles')}}" required>
                                                    <div class="help-block with-errors"></div>
            
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
            
                                               
                                                <div class="form-group">
                                                    <label for="password">{{ __('Password')}}</label>
                                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  >
                                                    <div class="help-block with-errors"></div>
            
                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="password-confirm">{{ __('Confirm Password')}}</label>
                                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company_name">{{ __('Company Name')}}</label>
                                                    <input id="company-name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ clean($user->company_name, 'titles')}}" placeholder="Company Name">
                                                    <div class="help-block with-errors" ></div>
                                                    @error('company_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
            
                                                <div class="form-group">
                                                    <label for="company_address">{{ __('Company Address')}}</label>
                                                    <input id="company-address" type="text" class="form-control @error('company_address') is-invalid @enderror" name="company_address"  value="{{ clean($user->company_address, 'titles')}}" placeholder="Company Address">
                                                    <div class="help-block with-errors" ></div>
                                                    @error('company_address')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
            
                                                <div class="form-group">
                                                    <label for="mobile_no">{{ __('Mobile No')}}<span class="text-red">*</span></label>
                                                    <input id="mobile-no" type="text" required class="form-control @error('mobile_no') is-invalid @enderror" name="mobile_no"  value="{{ clean($user->mobile_no, 'titles')}}" placeholder="Mobile No">
                                                    <div class="help-block with-errors" ></div>
                                                    @error('mobile_no')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <input type="hidden" id="token" name="token" value="{{ csrf_token() }}">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary form-control-right">{{ __('Update')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    
                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
