@extends('layouts.main') 
@section('title', 'Add User')
@section('content')
    <!-- push external head elements to head -->
     <!-- push external head elements to head -->
     @push('head')

     <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
     <link rel="stylesheet" href="{{ asset('plugins/summernote/dist/summernote-bs4.css') }}">
     <link rel="stylesheet" href="{{ asset('plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
     <link rel="stylesheet" href="{{ asset('plugins/mohithg-switchery/dist/switchery.min.css') }}">

 @endpush

    
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Add User')}}</h5>
                            <span>{{ __('Create new user, assign roles & permissions')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('Add User')}}</a>
                            </li>
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
                <div class="card ">
                    <div class="card-header">
                        <h3>{{ __('Add user')}}</h3>
                    </div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{ route('create-user') }}" >
                        @csrf
                            <div class="row">
                                <div class="col-sm-6">

                                    <div class="form-group">
                                        <label for="name">{{ __('Username')}}<span class="text-red">*</span></label>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="" placeholder="Enter user name" required>
                                        <div class="help-block with-errors"></div>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">{{ __('Email')}}<span class="text-red">*</span></label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter email address" required>
                                        <div class="help-block with-errors" ></div>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                      
                                    <div class="form-group">
                                        <label for="password">{{ __('Password')}}<span class="text-red">*</span></label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter password" required>
                                        <div class="help-block with-errors"></div>

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password-confirm">{{ __('Confirm Password')}}<span class="text-red">*</span></label>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Retype password" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="row">
                                    <div class="form-group col-sm-4 form-radio">
                                        <label for="action">{{ __('Action')}}</label>
                                                <div class="radio radio-outline radio-inline">
                                                    <label>
                                                        <input type="radio" value = "1" name="assigned" checked="checked">
                                                        <i class="helper"></i>{{ __('Assigned')}}
                                                    </label>
                                                </div>
                                                <div class="radio radio-outline radio-inline">
                                                    <label>
                                                        <input type="radio" value="0" name="assigned">
                                                        <i class="helper"></i>{{ __('Not Assigned')}}
                                                    </label>
                                                </div>
                                    </div>
                                    
                                    <div class="form-group col-sm-4">
                                        <label for="balance">{{ __('Sms Balance')}}</label>
                                        <input id="balance" type="number" class="form-control @error('balance') is-invalid @enderror" name="balance" value="{{ old('balance') }}"placeholder="Balance">
                                        <div class="help-block with-errors" ></div>
                                        @error('balance')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label for="validity">{{ __('Validity')}}</label>
                                        <select class="form-control select2">
                                            <option value="1">{{ __('1 Month')}}</option>
                                            <option value="2">{{ __('2 Month')}}</option>
                                            <option value="3">{{ __('3 Month')}}</option>
                                            <option value="4">{{ __('4 Month')}}</option>
                                            <option value="5">{{ __('5 Month')}}</option>
                                            <option value="6">{{ __('6 Month')}}</option>
                                            <option value="12">{{ __('12 Month')}}</option>
                                        </select>
                                        <div class="help-block with-errors" ></div>
                                        @error('validity')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                    
                                    
                                
                                </div>
                                <div class="col-md-6">
                                    <!-- Assign role & view role permisions -->
                                    <div class="form-group">
                                        <label for="role">{{ __('Assign Role')}}<span class="text-red">*</span></label>
                                        {!! Form::select('role', $roles, null,[ 'class'=>'form-control select2', 'placeholder' => 'Select Role','id'=> 'role', 'required'=> 'required']) !!}
                                    </div>
                                    <div class="form-group" >
                                        <label for="role">{{ __('Permissions')}}</label>
                                        <div id="permission" class="form-group" style="border-left: 2px solid #d1d1d1;">
                                            <span class="text-red pl-3">Select role first</span>
                                        </div>
                                        <input type="hidden" id="token" name="token" value="{{ csrf_token() }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="company_name">{{ __('Company Name')}}</label>
                                        <input id="company-name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}"placeholder="Company Name">
                                        <div class="help-block with-errors" ></div>
                                        @error('company_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="company_address">{{ __('Company Address')}}</label>
                                        <input id="company-address" type="text" class="form-control @error('company_address') is-invalid @enderror" name="company_address" value="{{ old('company_address') }}"placeholder="Company Address">
                                        <div class="help-block with-errors" ></div>
                                        @error('company_address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="entity_id">{{ __('Entity ID (Only for India)')}}</label>
                                        <input id="entity-id" type="text" class="form-control @error('entity_id') is-invalid @enderror" name="entity_id" value="{{ old('entity_id') }}"placeholder="Entity ID">
                                        <div class="help-block with-errors" ></div>
                                        @error('entity_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">{{ __('Submit')}}</button>
                                    </div>
                                </div>
                            </div>
                        
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/dist/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
        <script src="{{ asset('plugins/jquery.repeater/jquery.repeater.min.js') }}"></script>
        <script src="{{ asset('plugins/mohithg-switchery/dist/switchery.min.js') }}"></script>
        <script src="{{ asset('js/get-role.js') }}"></script>
        <script src="{{ asset('js/form-advanced.js') }}"></script>
    @endpush
@endsection
