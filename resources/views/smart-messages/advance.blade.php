@extends('layouts.main') 
@section('title', 'Smart Message Advance')
@section('content')
<!-- push external head elements to head -->
@push('head')

<link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/summernote/dist/summernote-bs4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/mohithg-switchery/dist/switchery.min.css') }}">

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
.invalid-feedback{
    display: block;
}
.img-responsive {
  display:block;
  max-width: 100%; // Part 1: Set a maximum relative to the parent
  height: auto; // Part 2: Scale the height according to the width, otherwise you get stretching
}

</style>
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-edit bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Smart SMS advance')}}</h5>
                            <span>{{ __('send advance smart message with minimum configuraion')}}</span>
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
                            <li class="breadcrumb-item active" aria-current="page">{{ __('advance')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <form class="smart-message-advance-from" enctype="multipart/form-data" method="POST" action="{{ route('send-smart-message-advance') }}" > 
            <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>{{ __('Smart advance Message')}}</h3></div>
                    <div class="card-body">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputUsername1">{{ __('Send To')}}</label>
                                <label class="custom-control custom-radio">
                                    <input type="radio" checked class="custom-control-input">
                                    <span class="custom-control-label">&nbsp;{{ __('Number/Numbers')}}</span>
                                </label>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                <label for="input">{{ __('Mobile Numbers')}}<span class="text-red">*</span></label>
                                <input type="text" id="mobile-numbers" value="{{ old('mobile_no') }}"   name="mobile_no" class="form-control">
                                <div class="text-center" style="position: relative;top: 7px;width:489px">
                                    <button id="clearMobileNumbers" class="btn btn-light" onclick="return false;">{{ __('Clear List')}}</button>
                                    <button  class="btn btn-light" onclick="return false;">{{__('Unique Nos')}}</button>
                                    <button  id="countMobileNumbers" onclick="return false;" class="btn btn-light">{{ __('Count Nos')}}</button>
                                </div>
                                <div class="help-block with-errors mt-25" ></div>
                                    @error('mobile_no')
                                    <div class="alert alert-danger" role="alert">
                                         {{ $message }}
                                      </div>
                                    @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="Template">{{ __('Choose Existing Template')}}</label>
                                <select class="form-control" id="template" name="template">
                                    <option value="">Choose Template</option>
                                    @foreach ( $templates as $template )
                                        <option value="{{$template->id}}" {{ old('template') == $template->id ? "selected" : "" }}>{{$template->template_name}}</option>
                                    @endforeach
                                </select> 
                                <div class="help-block with-errors mt-25" ></div>
                                    <div class="form-group">
                                        <label for="templateName">{{ __('Template Name')}}</label>
                                        <input type="text" class="form-control" value="{{ old('template_name') }}" name="template_name" id="template_name" placeholder="Template Name">
                                    </div>  
                                    <div class="help-block with-errors mt-25" ></div>
                                    @error('template_name')
                                    <div class="alert alert-danger" role="alert">
                                         {{ $message }}
                                      </div>
                                    @enderror  
                            </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
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
                                <div class="form-group col-md-6">
                                    <label for="exampleInputUsername1">{{ __('Send immediately')}}</label>
                                    <label class="custom-control custom-radio">
                                        <input type="radio" checked class="custom-control-input">
                                        <span class="custom-control-label">&nbsp;{{ __('Number/Numbers')}}</span>
                                    </label>
                                </div>
                            </div>    
                            <hr/>
                            <div class="form-group text-center mt-25">
                            <button type="submit" class="btn btn-primary mr-50 submit-btn" name="submit_virgin"  value="1" onclick="">{{ __('Submit')}}</button>
                            <button type="submit" class="btn btn-warning mr-50 submit-btn" name="submit_template" value="1" onclick="">{{ __('Submit & Save Template')}}</button>
                            <button class="btn btn-light">Cancel</button>
                            </div>
                    </div>
                </div>
            </div>
            </div>

            <div class="row">
            <div class="col-md-6">
                <div class="card" id="card-1">
                    <div class="card-header" style="display: block"><h3>{{ __('Carousel Configuration For Card 1')}}</h3> 
                        <span style="float:right;position: relative;top:-25px"><input type="checkbox" name="card_1_check"  class="js-success card-1-check" checked /></span>
                    </div>
                    <div class="card-body">
                            <div class="form-group">
                                <label>{{ __('Image Title')}}</label>
                                <input type="file" name="file_card_1" class="file-upload-default file-upload-default-card-1">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info file-upload-info-card-1" value="{{ old('image_title_card_1') }}"  name="image_title_card_1" placeholder="Image Title">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse file-upload-browse-card-1 btn btn-primary" type="button">{{ __('Upload')}}</button>
                                    </span> 
                                </div>
                                <div class="alert alert-warning  fade show" role="alert" id="image-alert-card-1">
                                    <img src="" id="card_1_img" />
                                </div>
                                <div class="help-block with-errors mt-25" ></div>
                                    @error('file_card_1')
                                    <div class="alert alert-danger" role="alert">
                                         {{ $message }}
                                      </div>
                                    @enderror
                            </div> 

                            <div class="form-group">
                                <label for="messageText">{{ __('Message')}}</label>
                                <textarea class="form-control" name="message_card_1"  value="{{ old('message_card_1') }}" id="messageTextCard1" rows="4"></textarea>
                                <div class="help-block with-errors mt-25" ></div>
                                @error('message_card_1')
                                <div class="alert alert-danger" role="alert">
                                     {{ $message }}
                                  </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="callTitileCard1">{{ __('Call Title')}}</label>
                                <input type="text" class="form-control" value="{{ old('call_title_card_1') }}" name="call_title_card_1" id="callTitileCard1" placeholder="Call Title">
                            </div>
                            <div class="form-group">
                                <label for="callNumberCard1">{{ __('Call Number')}}</label>
                                <input type="number" class="form-control" value="{{ old('call_number_card_1') }}" name="call_number_card_1" id="callNumberCard1" placeholder="Call Number">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle1Card1">{{ __('Open Url Title 1')}}</label>
                                <input type="text" class="form-control" value="{{ old('open_url_title_1_card_1') }}" name="open_url_title_1_card_1" id="openUrlTitle1Card1" placeholder="Open Url Title 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrl1Card1">{{ __('Open Url 1')}}</label>
                                <input type="url" class="form-control" value="{{ old('open_url_1_card_1') }}" name="open_url_1_card_1" id="openUrl1Card1" placeholder="Open Url 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle2Card1">{{ __('Open Url Title 2')}}</label>
                                <input type="text" class="form-control" value="{{ old('open_url_title_2_card_1') }}" name="open_url_title_2_card_1" id="openUrlTitle2Card1" placeholder="Open Url Title 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrl2Card1">{{ __('Open Url 2')}}</label>
                                <input type="url" class="form-control" value="{{ old('open_url_2_card_1') }}" name="open_url_2_card_1" id="openUrl2Card1" placeholder="Open Url 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle3Card1">{{ __('Open Url Title 3')}}</label>
                                <input type="text" class="form-control" value="{{ old('open_url_title_3_card_1') }}" name="open_url_title_3_card_1" id="openUrlTitle3Card1" placeholder="Open Url Title 3">
                            </div>
                            <div class="form-group">
                                <label for="openUrl3Card1">{{ __('Open Url 3')}}</label>
                                <input type="url" class="form-control" value="{{ old('open_url_3_card_1') }}" name="open_url_3_card_1" id="openUrl3Card1" placeholder="Open Url 3">
                            </div>
                            <div class="form-group text-center" >
                            <button type="button" class="btn btn-lg btn-danger mr-2 card-1-preview">{{ __('Preview')}}</button>
                            </div>   
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card" id="card-2">
                    <div class="card-header" style="display: block"><h3>{{ __('Carousel Configuration For Card 2')}}</h3> 
                        <span style="float:right;position: relative;top:-25px"><input type="checkbox" name="card_2_check"  class="selected-card js-success-card-2 card-2-check" checked /></span>
                    </div>
                    <div class="card-body">
                            <div class="form-group">
                                <label>{{ __('Image Title')}}</label>
                                <input type="file" name="file_card_2" class="file-upload-default file-upload-default-card-2">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info file-upload-info-card-2" value="{{ old('image_title_card_2') }}"  name="image_title_card_2" placeholder="Image Title">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse file-upload-browse-card-2 btn btn-primary" type="button">{{ __('Upload')}}</button>
                                    </span> 
                                </div>
                                <div class="alert alert-warning  fade show image-alert-card-2" role="alert" id="image-alert-card-2">
                                    
                                </div>
                                <div class="help-block with-errors mt-25" ></div>
                                @error('file_card_2')
                                <div class="alert alert-danger" role="alert">
                                     {{ $message }}
                                  </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="messageText">{{ __('Message')}}</label>
                                <textarea class="form-control messageTextcard2" name="message_card_2"  value="{{ old('message_card_2') }}" id="messageTextcard2" rows="4"></textarea>
                                <div class="help-block with-errors mt-25" ></div>
                                @error('message_card_2')
                                <div class="alert alert-danger" role="alert">
                                     {{ $message }}
                                  </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="callTitilecard2">{{ __('Call Title')}}</label>
                                <input type="text" class="form-control callTitilecard2" value="{{ old('call_title_card_2') }}" name="call_title_card_2" id="callTitilecard2" placeholder="Call Title">
                            </div>
                            <div class="form-group">
                                <label for="callNumbercard2">{{ __('Call Number')}}</label>
                                <input type="number" class="form-control callNumberCard2" value="{{ old('call_number_card_2') }}" name="call_number_card_2" id="callNumbercard2" placeholder="Call Number">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle1card2">{{ __('Open Url Title 1')}}</label>
                                <input type="text" class="form-control openUrlTitle1Card2" value="{{ old('open_url_title_1_card_2') }}" name="open_url_title_1_card_2" id="openUrlTitle1card2" placeholder="Open Url Title 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrl1card2">{{ __('Open Url 1')}}</label>
                                <input type="url" class="form-control openUrl1card2" value="{{ old('open_url_1_card_2') }}" name="open_url_1_card_2" id="openUrl1card2" placeholder="Open Url 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle2card2">{{ __('Open Url Title 2')}}</label>
                                <input type="text" class="form-control openUrlTitle2card2" value="{{ old('open_url_title_2_card_2') }}" name="open_url_title_2_card_2" id="openUrlTitle2card2" placeholder="Open Url Title 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrl2card2">{{ __('Open Url 2')}}</label>
                                <input type="url" class="form-control openUrl2card2" value="{{ old('open_url_2_card_2') }}" name="open_url_2_card_2" id="openUrl2card2" placeholder="Open Url 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle3card2">{{ __('Open Url Title 3')}}</label>
                                <input type="text" class="form-control openUrlTitle3card2" value="{{ old('open_url_title_3_card_2') }}" name="open_url_title_3_card_2" id="openUrlTitle3card2" placeholder="Open Url Title 3">
                            </div>
                            <div class="form-group">
                                <label for="openUrl3card2">{{ __('Open Url 3')}}</label>
                                <input type="url" class="form-control openUrl3card2" value="{{ old('open_url_3_card_2') }}" name="open_url_3_card_2" id="openUrl3card2" placeholder="Open Url 3">
                            </div>
                            <div class="form-group text-center" >
                            <button type="button" class="btn btn-lg btn-danger mr-2 card-2-preview">{{ __('Preview')}}</button>
                            </div>   
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card" id="card-3">
                    <div class="card-header" style="display: block"><h3>{{ __('Carousel Configuration For Card 3')}}</h3> 
                        <span style="float:right;position: relative;top:-25px"><input type="checkbox" name="card_3_check"  class="selected-card js-success-card-3 card-3-check" checked /></span>
                    </div>
                    <div class="card-body">
                            <div class="form-group">
                                <label>{{ __('Image Title')}}</label>
                                <input type="file" name="file_card_3" class="file-upload-default file-upload-default-card-3">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info file-upload-info-card-3" value="{{ old('image_title_card_3') }}"  name="image_title_card_3" placeholder="Image Title">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse file-upload-browse-card-3 btn btn-primary" type="button">{{ __('Upload')}}</button>
                                    </span> 
                                </div>
                                <div class="alert alert-warning  fade show" role="alert" id="image-alert-card-3">
                                    
                                </div>
                                <div class="help-block with-errors mt-25" ></div>
                                @error('file_card_3')
                                <div class="alert alert-danger" role="alert">
                                     {{ $message }}
                                  </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="messageText">{{ __('Message')}}</label>
                                <textarea class="form-control messageTextcard3" name="message_card_3"  value="{{ old('message_card_3') }}" id="messageTextcard3" rows="4"></textarea>
                                <div class="help-block with-errors mt-25" ></div>
                                @error('message_card_3')
                                <div class="alert alert-danger" role="alert">
                                     {{ $message }}
                                  </div>
                                @enderror
                               
                            </div>
                            <div class="form-group">
                                <label for="callTitilecard3">{{ __('Call Title')}}</label>
                                <input type="text" class="form-control callTitilecard3" value="{{ old('call_title_card_3') }}" name="call_title_card_3" id="callTitilecard3" placeholder="Call Title">
                            </div>
                            <div class="form-group">
                                <label for="callNumbercard3">{{ __('Call Number')}}</label>
                                <input type="number" class="form-control callNumbercard3" value="{{ old('call_number_card_3') }}" name="call_number_card_3" id="callNumbercard3" placeholder="Call Number">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle1card3">{{ __('Open Url Title 1')}}</label>
                                <input type="text" class="form-control openUrlTitle1card3" value="{{ old('open_url_title_1_card_3') }}" name="open_url_title_1_card_3" id="openUrlTitle1card3" placeholder="Open Url Title 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrl1card3">{{ __('Open Url 1')}}</label>
                                <input type="url" class="form-control openUrl1card3" value="{{ old('open_url_1_card_3') }}" name="open_url_1_card_3" id="openUrl1card3" placeholder="Open Url 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle2card3">{{ __('Open Url Title 2')}}</label>
                                <input type="text" class="form-control openUrlTitle2card3" value="{{ old('open_url_title_2_card_3') }}" name="open_url_title_2_card_3" id="openUrlTitle2card3" placeholder="Open Url Title 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrl2card3">{{ __('Open Url 2')}}</label>
                                <input type="url" class="form-control openUrl2card3" value="{{ old('open_url_2_card_3') }}" name="open_url_2_card_3" id="openUrl2card3" placeholder="Open Url 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle3card3">{{ __('Open Url Title 3')}}</label>
                                <input type="text" class="form-control openUrlTitle3card3" value="{{ old('open_url_title_3_card_3') }}" name="open_url_title_3_card_3" id="openUrlTitle3card3" placeholder="Open Url Title 3">
                            </div>
                            <div class="form-group">
                                <label for="openUrl3card3">{{ __('Open Url 3')}}</label>
                                <input type="url" class="form-control openUrl3card3" value="{{ old('open_url_3_card_3') }}" name="open_url_3_card_3" id="openUrl3card3" placeholder="Open Url 3">
                            </div>
                            <div class="form-group text-center" >
                            <button type="button" class="btn btn-lg btn-danger mr-2 card-3-preview">{{ __('Preview')}}</button>
                            </div>   
                    </div>
                </div>
            </div>
        
            <div class="col-md-6">
                <div class="card" id="card-4">
                    <div class="card-header" style="display: block"><h3>{{ __('Carousel Configuration For Card 4')}}</h3> 
                        <span style="float:right;position: relative;top:-25px"><input type="checkbox" name="card_4_check"  class="selected-card js-success-card-4 card-4-check" checked /></span>
                    </div>
                    <div class="card-body">
                            <div class="form-group">
                                <label>{{ __('Image Title')}}</label>
                                <input type="file" name="file_card_4" class="file-upload-default file-upload-default-card-4">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info file-upload-info-card-4" value="{{ old('image_title_card_4') }}"  name="image_title_card_4" placeholder="Image Title">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse file-upload-browse-card-4 btn btn-primary" type="button">{{ __('Upload')}}</button>
                                    </span> 
                                </div>
                                <div class="alert alert-warning  fade show" role="alert" id="image-alert-card-4">
                                    
                                </div>
                                <div class="help-block with-errors mt-25" ></div>
                                @error('file_card_4')
                                <div class="alert alert-danger" role="alert">
                                     {{ $message }}
                                  </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="messageText">{{ __('Message')}}</label>
                                <textarea class="form-control messageTextCard4" name="message_card_4"  value="{{ old('message_card_4') }}" id="messageTextcard4" rows="4"></textarea>
                                <div class="help-block with-errors" ></div>
                                <div class="help-block with-errors mt-25" ></div>
                                @error('message_card_4')
                                <div class="alert alert-danger" role="alert">
                                     {{ $message }}
                                  </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="callTitilecard4">{{ __('Call Title')}}</label>
                                <input type="text" class="form-control callTitilecard4" value="{{ old('call_title_card_4') }}" name="call_title_card_4" id="callTitilecard4" placeholder="Call Title">
                            </div>
                            <div class="form-group">
                                <label for="callNumbercard4">{{ __('Call Number')}}</label>
                                <input type="number" class="form-control callNumbercard4"  value="{{ old('call_number_card_4') }}" name="call_number_card_4" id="callNumbercard4" placeholder="Call Number">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle1card4">{{ __('Open Url Title 1')}}</label>
                                <input type="text" class="form-control openUrlTitle1card4" value="{{ old('open_url_title_1_card_4') }}" name="open_url_title_1_card_4" id="openUrlTitle1card4" placeholder="Open Url Title 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrl1card4">{{ __('Open Url 1')}}</label>
                                <input type="url" class="form-control openUrl1card4" value="{{ old('open_url_1_card_4') }}" name="open_url_1_card_4" id="openUrl1card4" placeholder="Open Url 1">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle2card4">{{ __('Open Url Title 2')}}</label>
                                <input type="text" class="form-control openUrlTitle2card4" value="{{ old('open_url_title_2_card_4') }}" name="open_url_title_2_card_4" id="openUrlTitle2card4" placeholder="Open Url Title 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrl2card4">{{ __('Open Url 2')}}</label>
                                <input type="url" class="form-control openUrl2card4" value="{{ old('open_url_2_card_4') }}" name="open_url_2_card_4" id="openUrl2card4" placeholder="Open Url 2">
                            </div>
                            <div class="form-group">
                                <label for="openUrlTitle3card4">{{ __('Open Url Title 3')}}</label>
                                <input type="text" class="form-control openUrlTitle3card4" value="{{ old('open_url_title_3_card_4') }}" name="open_url_title_3_card_4" id="openUrlTitle3card4" placeholder="Open Url Title 3">
                            </div>
                            <div class="form-group">
                                <label for="openUrl3card4">{{ __('Open Url 3')}}</label>
                                <input type="url" class="form-control openUrl3card4" value="{{ old('open_url_3_card_4') }}" name="open_url_3_card_4" id="openUrl3card4" placeholder="Open Url 3">
                            </div>
                            <div class="form-group text-center" >
                            <button type="button" class="btn btn-lg btn-danger mr-2 card-4-preview">{{ __('Preview')}}</button>
                            </div>   
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" value="{{url('/')}}" id="url" name="url">
        <input type ="hidden"  class="card1_img_template" value="{{ old('card1_img_template') }}" name="card1_img_template" />
        <input type ="hidden"  class="card2_img_template" value="{{ old('card2_img_template') }}" name="card2_img_template" />
        <input type ="hidden"  class="card3_img_template" value="{{ old('card3_img_template') }}" name="card3_img_template" />
        <input type ="hidden"  class="card4_img_template" value="{{ old('card4_img_template') }}" name="card4_img_template" />
    </form>

    <!-- Modal for Image -->
    <div class="modal fade" id="card_image" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-body">
             <img src=""  class="img-responsive card_image_src"  />
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
     <script src="{{ asset('js/form-components.js') }}"></script>
     <script src="{{ asset('js/form-advanced.js') }}"></script>
 @endpush
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        @if(old('template')!='')
            var templateId = $('#template').val();
            updateTemplate(templateId);
        @endif
        
        $('#template').on('change', function () {
            var templateId = this.value;
            updateTemplate(templateId);
        });
    }); 

    function updateTemplate(templateId){
        $.ajax({
                url: "{{url('api/fetch-template')}}",
                type: "POST",
                data: {
                   id: templateId,
                   _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    console.log(result);
                    /**
                        set values in form 
                    **/
                    //set card active
                    setCardActive(result.template);
                    //set data for card one
                    if(result.template.card_1_enable)
                        setCardOneData(result.template);
                    //set data for card two
                    if(result.template.card_2_enable)
                        setCardTwoData(result.template);
                    //  //set data for card three
                    if(result.template.card_3_enable)
                        setCardThreeData(result.template);
                    //   //set data for card four
                    if(result.template.card_4_enable)
                        setCardFourData(result.template);

                }
            });
    }

    function changeSwitchery(element, checked) {
        if ( ( element.is(':checked') && checked == false ) || ( !element.is(':checked') && checked == true ) ) {
            element.parent().find('.switchery').trigger('click');
        }
    }

    function setCardActive(template){
        var card1 = $('.card-1-check');
        var card2 = $('.card-2-check');
        var card3 = $('.card-3-check');
        var card4 = $('.card-4-check');

        changeSwitchery(card1, template.card_1_enable);
        changeSwitchery(card2, template.card_2_enable);
        changeSwitchery(card3, template.card_3_enable);
        changeSwitchery(card4, template.card_4_enable);
    }

    //setting data for card one
    function setCardOneData(template){
        var base_path = $("#url").val();
        $(".file-upload-info-card-1").val(template.image_title_card_1);
        $("#messageTextCard1").val(template.message_card_1);
        $("#callTitileCard1").val(template.call_title_card_1);
        $("#callNumberCard1").val(template.call_number_card_1);
        $("#openUrlTitle1Card1").val(template.open_url_title_1_card_1);
        $("#openUrl1Card1").val(template.open_url_1_card_1);
        $("#openUrlTitle2Card1").val(template.open_url_title_2_card_1);
        $("#openUrl2Card1").val(template.open_url_2_card_1);
        $("#openUrlTitle3Card1").val(template.open_url_title_3_card_1);
        $("#openUrl3Card1").val(template.open_url_title_3_card_1);
        $("#image-alert-card-1").html(template.image_card_1);
        $("#image-alert-card-1").append('<img src="" data-target="#card_image" data-toggle="modal" class="rounded" style="height:50px;width:50px;margin-left:50px " id="card_1_img" />');
        $("#card_1_img").attr("src",base_path+"/uploads/"+template.image_card_1);
        $(".card_image_src").attr("src",base_path+"/uploads/"+template.image_card_1);
        $(".card1_img_template").val(template.image_card_1);
    }

    // //setting data for card tow
    function setCardTwoData(template){
        var base_path = $("#url").val();
        $(".file-upload-info-card-2").val(template.image_title_card_2);
        $(".messageTextCard2").val(template.message_card_2);
        $(".callTitileCard2").val(template.call_title_card_2);
        $(".callNumberCard2").val(template.call_number_card_2);
        $(".openUrlTitle1Card2").val(template.open_url_title_1_card_2);
        $(".openUrl1Card2").val(template.open_url_1_card_2);
        $(".openUrlTitle2Card2").val(template.open_url_title_2_card_2);
        $(".openUrl2Card2").val(template.open_url_2_card_2);
        $(".openUrlTitle3Card2").val(template.open_url_title_3_card_2);
        $(".openUrl3Card2").val(template.open_url_title_3_card_2);
        $(".image-alert-card-2").html(template.image_card_2);
        $(".image-alert-card-2").append('<img src="" data-target="#card_image" data-toggle="modal" class="rounded" style="height:50px;width:50px;margin-left:50px " id="card_2_img" />');
        $("#card_2_img").attr("src",base_path+"/uploads/"+template.image_card_2);
        $(".card_image_src").attr("src",base_path+"/uploads/"+template.image_card_2);
        $(".card2_img_template").val(template.image_card_2);
    }

    //setting data for card three
    function setCardThreeData(template){
        var base_path = $("#url").val();
        $(".file-upload-info-card-3").val(template.image_title_card_3);
        $(".messageTextCard3").val(template.message_card_3);
        $(".callTitileCard3").val(template.call_title_card_3);
        $(".callNumberCard3").val(template.call_number_card_3);
        $(".openUrlTitle1Card3").val(template.open_url_title_1_card_3);
        $(".openUrl1Card3").val(template.open_url_1_card_3);
        $(".openUrlTitle2Card3").val(template.open_url_title_2_card_3);
        $(".openUrl2Card3").val(template.open_url_2_card_3);
        $(".openUrlTitle3Card3").val(template.open_url_title_3_card_3);
        $(".openUrl3Card3").val(template.open_url_title_3_card_3);
        $("#image-alert-card-3").html(template.image_card_3);
        $("#image-alert-card-3").append('<img src="" data-target="#card_image" data-toggle="modal" class="rounded" style="height:50px;width:50px;margin-left:50px " id="card_3_img" />');
        $("#card_3_img").attr("src",base_path+"/uploads/"+template.image_card_3);
        $(".card_image_src").attr("src",base_path+"/uploads/"+template.image_card_3);
        $(".card3_img_template").val(template.image_card_3);
    }

    // //setting data for card four
    function setCardFourData(template){
        var base_path = $("#url").val();
        $(".file-upload-info-card-4").val(template.image_title_card_4);
        $(".messageTextCard4").val(template.message_card_4);
        $(".callTitileCard4").val(template.call_title_card_4);
        $(".callNumberCard4").val(template.call_number_card_4);
        $(".openUrlTitle1Card4").val(template.open_url_title_1_card_4);
        $(".openUrl1Card4").val(template.open_url_1_card_4);
        $(".openUrlTitle2Card4").val(template.open_url_title_2_card_4);
        $(".openUrl2Card4").val(template.open_url_2_card_4);
        $(".openUrlTitle3Card4").val(template.open_url_title_3_card_4);
        $(".openUrl3Card4").val(template.open_url_title_3_card_4);
        $("#image-alert-card-4").html(template.image_card_4);
        $("#image-alert-card-4").append('<img src="" data-target="#card_image" data-toggle="modal" class="rounded" style="height:50px;width:50px;margin-left:50px " id="card_4_img" />');
        $("#card_4_img").attr("src",base_path+"/uploads/"+template.image_card_4);
        $(".card_image_src").attr("src",base_path+"/uploads/"+template.image_card_4);
        $(".card4_img_template").val(template.image_card_4);
    }
     $(document).ready(function () {   
        $(document).on('click', '#card_1_img', function(){
            console.log($("#card_1_img").attr("src"));
            $(".card_image_src").attr("src",$("#card_1_img").attr("src"));
        })
        $(document).on('click', '#card_2_img', function(){
            console.log($("#card_2_img").attr("src"));
            $(".card_image_src").attr("src",$("#card_2_img").attr("src"));
        })
        $(document).on('click', '#card_3_img', function(){
            console.log($("#card_3_img").attr("src"));
            $(".card_image_src").attr("src",$("#card_3_img").attr("src"));
        })
        $(document).on('click', '#card_4_img', function(){
            console.log($("#card_4_img").attr("src"));
            $(".card_image_src").attr("src",$("#card_4_img").attr("src"));
        })   
    });   
       
 </script>
 