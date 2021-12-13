@extends('layouts.main') 
@section('title', 'Smart Message Basic')
@section('content')
<!-- push external head elements to head -->
@push('head')

<link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/summernote/dist/summernote-bs4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/mohithg-switchery/dist/switchery.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/jquery-toast-plugin/dist/jquery.toast.min.css')}}">

@endpush
<style>
    .bootstrap-tagsinput{
        height:100px;
        width:500px;
    }
.hr {
  margin-top: 1rem;
  margin-bottom: 1rem;
  border: 0;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  margin-bottom: 22px !important;
}

</style>
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-edit bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Smart SMS Basic')}}</h5>
                            <span>{{ __('send basic smart message with minimum configuraion')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{route('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="#">{{ __('Smart Message')}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Basic')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <form class="smart-message-basic-from" enctype="multipart/form-data" method="POST" action="{{ route('send-smart-message-basic') }}" >
            {{-- @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
          @endif --}}

            <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h3>{{ __('Smart Basic Message')}}</h3></div>
                    <div class="card-body">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputUsername1">{{ __('Send To')}}</label>
                                <label class="custom-control custom-radio">
                                    <input type="radio" checked class="custom-control-input">
                                    <span class="custom-control-label">&nbsp;{{ __('Number/Numbers')}}</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                <label for="input">{{ __('Mobile Numbers')}}<span class="text-red">*</span></label>
                                <input type="text" id="mobile-numbers" value="{{ old('mobile_no') }}"   name="mobile_no" class="form-control">
                                <span class="text-center" style="position: relative;top: 7px">
                                    <button id="clearMobileNumbers" class="btn btn-light">{{ __('Clear List')}}</button>
                                    <button  class="btn btn-light">{{__('Unique Nos')}}</button>
                                    <button  id="countMobileNumbers" onclick="return false;" class="btn btn-light">{{ __('Count Nos')}}</button>
                                </span>
                                <div class="help-block with-errors" ></div>
                                        @error('mobile_no')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                            </div>
                            </div>
                            <div class="form-group">
                                <label for="action">{{ __('Message')}}</label>
                                <br>
                                <label class="custom-control custom-radio radio-inline">
                                    <input type="radio" name="message_type" value="TEXT" checked class="custom-control-input ">
                                    <span class="custom-control-label">&nbsp;{{ __('Text')}}</span>
                                </label> 
                                <label class="custom-control custom-radio radio-inline" onclick="window.open('https://www.google.co.in/inputtools/try/','_blank')">
                                    <input type="radio"  name="message_type" value="LANGUAGE" class="custom-control-input">
                                    <span class="custom-control-label">&nbsp;{{ __('Language')}}</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Image Title')}}</label>
                                <input type="file" name="file" class="file-upload-default file-upload-default-basic">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info file-upload-info-basic" value="{{ old('image_title') }}"  name="image_title" placeholder="Image Title">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse file-upload-browse-basic btn btn-primary" type="button">{{ __('Upload')}}</button>
                                    </span> 
                                </div>
                                <div class="alert alert-warning alert-dismissible fade show" role="alert" id="image-alert-basic">
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="messageText">{{ __('Message')}}</label>
                                <textarea class="form-control" name="message"  value="{{ old('message') }}" id="messageText" rows="4">{{ old('message') }}</textarea>
                                <div id="counter"></div>
                                <div class="help-block with-errors" ></div>
                                @error('message')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">{{ __('Send immediately')}}</label>
                                <label class="custom-control custom-radio">
                                    <input type="radio" checked class="custom-control-input">
                                    <span class="custom-control-label">&nbsp;{{ __('Number/Numbers')}}</span>
                                </label>
                            </div>
                            <hr/>
                            <div class="form-group text-center mt-25">
                            <button type="submit" class="btn btn-primary mr-50">{{ __('Submit')}}</button>
                            <button class="btn btn-light">Cancel</button>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h3>{{ __('Open Url Configuration')}}</h3></div>
                    <div class="card-body">
                            <div class="form-group">
                                <label for="callTitile">{{ __('Call Title')}}</label>
                                <input type="text" class="form-control" value="{{ old('call_title') }}" name="call_title" id="callTitile" placeholder="Call Title">
                            </div>
                            <div class="form-group">
                                <label for="callNumber">{{ __('Call Number')}}</label>
                                <input type="number" class="form-control" value="{{ old('call_number') }}" name="call_number" id="callNumber" placeholder="Call Number">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle1">{{ __('Open Url Title 1')}}</label>
                                <input type="text" class="form-control" value="{{ old('open_url_title_1') }}" name="open_url_title_1" id="openUrlTitle1" placeholder="Open Url Title 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrl1">{{ __('Open Url 1')}}</label>
                                <input type="url" class="form-control" value="{{ old('open_url_1') }}" name="open_url_1" id="openUrl1" placeholder="Open Url 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle2">{{ __('Open Url Title 2')}}</label>
                                <input type="text" class="form-control" value="{{ old('open_url_title_2') }}" name="open_url_title_2" id="openUrlTitle2" placeholder="Open Url Title 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrl2">{{ __('Open Url 2')}}</label>
                                <input type="url" class="form-control" value="{{ old('open_url_2') }}" name="open_url_2" id="openUrl2" placeholder="Open Url 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle3">{{ __('Open Url Title 3')}}</label>
                                <input type="text" class="form-control" value="{{ old('open_url_title_3') }}" name="open_url_title_3" id="openUrlTitle3" placeholder="Open Url Title 3">
                            </div>
                            <div class="form-group">
                                <label for="openUrl3">{{ __('Open Url 3')}}</label>
                                <input type="url" class="form-control" value="{{ old('open_url_3') }}" name="open_url_3" id="openUrl3" placeholder="Open Url 3">
                            </div>
                            <div class="form-group text-center" >
                            <button type="button" class="btn btn-lg btn-danger mr-2 ">{{ __('Preview')}}</button>
                            </div>   
                    </div>
                </div>
            </div>  
        </div>
    </form>

       
    </div>
     <!-- push external js -->
     @push('script')
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
     <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
     <script src="{{ asset('plugins/summernote/dist/summernote-bs4.min.js') }}"></script>
     <script src="{{ asset('plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
     <script src="{{ asset('plugins/jquery.repeater/jquery.repeater.min.js') }}"></script>
     <script src="{{ asset('plugins/mohithg-switchery/dist/switchery.min.js') }}"></script>
     <script src="{{ asset('js/form-components.js') }}"></script>
   
     <script src="{{ asset('js/form-advanced.js') }}"></script>
     <script src="{{ asset('plugins/jquery-toast-plugin/dist/jquery.toast.min.js')}}"></script>
        
     <script src="{{ asset('js/alerts.js')}}"></script>
     
     <script>
        $(document).ready( function() {
            @if(count($errors)>0)
                @foreach ($errors->all() as $error )
                    showDangerToast("{{$error}}");
                @endforeach
            @endif
        });
    </script>
 @endpush
@endsection