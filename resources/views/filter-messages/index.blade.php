@extends('layouts.main') 
@section('title', 'Filter Messages')
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
.bootstrap-tagsinput {
    /* height: 100px; */
    width: 500px;
    height: 35px;
    width: 200px;
    font-family: Verdana, Tahoma, Arial, Helvetica, sans-serif;
    font-size: 82%;
    overflow-x: hidden;
    overflow-y: scroll;
}
</style>
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="ik ik-inbox bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __('RCS Message Filter')}}</h5>
                        <span>{{ __('Upload file to check RCS eligibilty of mobile number')}}</span>
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
                            <a href="#">Filters</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">RCS Message Filter</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
<!-- upload file here -->
<div class="row layout-wrap" id="layout-wrap">
    <div class="col-12 list-item">
        <div class="card d-flex flex-row mb-3">
            <div class="d-flex flex-grow-1 min-width-zero card-content">
                <div class="card-body align-self-center d-flex flex-column flex-md-row justify-content-between min-width-zero align-items-md-center">
                     <form class="form-inline" enctype="multipart/form-data" method="POST" action="{{ route('store-filter-messages') }}" >
                        @csrf
                        <div class="form-group pr-10">
                            <label class="pr-10" for="fileName">{{ __('File Name')}}<span class="text-red">*</span></label>
                            <input type="text" class="form-control pr-10" value="{{ old('file_name') }}" name="file_name" id="fileName" placeholder="File Name">
                            <div class="help-block with-errors mt-25" ></div>
                        </div>
                        <div class="form-group pr-10">
                            <label class="pr-10">{{ __('Upload CSV File')}}<span class="text-red">*</span></label>
                            <input type="file" name="csv_file" class="file-upload-default file-upload-default-basic mb-2 pr-10">
                            <div class="input-group col-xs-12" style="top:7px">
                                <input type="text" class="form-control file-upload-info file-upload-info-basic" value=""  name="" placeholder="upload csv file">
                                <span class="input-group-append">
                                <button class="file-upload-browse file-upload-browse-basic btn btn-primary" type="button">{{ __('Upload')}}</button>
                                </span> 
                            </div> 
                            <div class="help-block with-errors mt-25 " ></div>
                        </div>
                        <div class="form-group pr-10">
                            <div class="form-group">
                            <label class="pr-10" for="input">{{ __('Tags')}}</label>
                            <textarea type="text" id="tags" value="{{ old('tags') }}"   name="tags" class="form-control"> </textarea>
                        </div>
                        </div>
                        <div class="form-group text-center ">
                            <button type="submit" style="height: 35px" class="btn btn-primary mr-10">{{ __('Submit')}}</button>
                            <a class="btn btn-warning " style="height: 35px"  download="Sample Data" href="{{ url('uploads/csv/sample_file.csv  ') }}" title="Sample Data">{{ __('Download Sample')}}</a>
                        </div>
                     </form>
                </div>
            </div>
        </div>
    </div>  
</div>
    <!-- end file upload here -->

    <div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h3>{{ __('RCS Message Filter')}}</h3></div>
            <div class="card-body">
                <table id="data_table" class="table table-striped table-bordered nowrap table-responsive">
                    <thead>
                        <tr>
                            <th>{{ __('Id')}}</th>
                            <th class="nosort">{{ __('File Name')}}</th>
                            <th>{{ __('User Name')}}</th>
                            <th>{{ __('User Email')}}</th>
                            <th>{{ __('User ID')}}</th>
                            <th>{{ __('User Contact')}}</th>
                            <th>{{ __('Valid Count')}}</th>
                            <th>{{ __('Invalid Count')}}</th>
                            <th>{{ __('Total Count')}}</th>
                            <th>{{ __('Uploaded File')}}</th>
                            <th>{{ __('Result')}}</th>
                            <th>{{ __('Uploaded Time')}}</th>
                            <th>{{ __('Filtered Time')}}</th>
                            <th>{{ __('Tags')}}</th>
                            <th>{{ __('Status')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($filters))
                            @foreach($filters as $filter)
                            <tr>
                                <td>{{$filter->id}}</td>
                                <td>{{$filter->name}}</td>
                                <td>{{$filter->username}}</td>
                                <td>{{$filter->email}}</td>
                                <td>{{$filter->user_id}}</td>
                                <td>{{$filter->mobile_no}}</td>
                                <td>{{$filter->valid_counts}}</td>
                                <td>{{$filter->invalid_counts}}</td>
                                <td>{{$filter->total_counts}}</td>
                                <td><a class="badge badge-info" download="Uploaded Data" href="{{ url('uploads/'.$filter->uploaded_file) }}" title="Uploaded Data">Download</a></td>
                                <td>
                                    @if($filter->not_reachable_users_file!='')
                                        <a class="badge badge-warning mb-10" download="Not Reachable Data" href="{{ url('uploads/'.$filter->not_reachable_users_file) }}" title="Reachable Data">Not Reachable</a>
                                    @endif
                                    @if($filter->reachable_users_file!='')
                                        <a class="badge badge-primary" download="Reachable Data" href="{{ url('uploads/'.$filter->reachable_users_file) }}" title="Reachable Data">Reachable</a>
                                    @endif
                                    @if($filter->downloaded_file!='')
                                        <a class="badge badge-success" download="Complete Data" href="{{ url('uploads/'.$filter->downloaded_file) }}" title="Complete Data">Complete</a>
                                    @elseif($filter->error_file!='')
                                    <a class="badge badge-danger" download="Error File" href="{{ url('uploads/'.$filter->error_file) }}" title="Error File">Download Errors</a>
                                    @else
                                        <label class="badge badge-warning mt-10">Not Available</label>
                                    @endif
                                    
                                </td>
                                <td>{{$filter->created_at}}</td>
                                <td>{{$filter->updated_at}}</td>
                                <td>
                                    @if($filter->tags!='')
                                        @foreach (explode(',',$filter->tags) as $tag )
                                            <label class="badge badge-primary">{{ucfirst($tag)}}</label>  
                                        @endforeach
                                    @else
                                        -
                                    @endif    
                                </td>
                                <td>
                                    @if($filter->status==0)
                                        <label class="badge badge-warning mt-10">InQueue</label>
                                    @endif
                                    @if($filter->status==1)
                                        <label class="badge badge-success mt-10">Success</label>
                                    @endif
                                    @if($filter->status==2)
                                        <label class="badge badge-danger mt-10">Failed</label>
                                    @endif
                                </td>
                                
                            </tr>
                            @endforeach
                        @endif    
                    </tbody>
                </table>
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