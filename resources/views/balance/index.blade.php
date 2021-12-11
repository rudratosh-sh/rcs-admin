@extends('layouts.main') 
@section('title', 'Balance Managment')
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
.select2-container {
    padding-left: 14px;
}
</style>
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="ik ik-inbox bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __('RCS Balance Editor')}}</h5>
                        <span>{{ __('Credit & Debit balance for users')}}</span>
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
                            <label for="users">Users: &nbsp;</label>
                            <select id="users" required name="user" class="form-control select2">
                              <option value="" selected disabled>Select User</option>
                               @foreach($users as $key => $user)
                               <option value="{{$user->id}}" {{ (old("user") == $user->id ? "selected":"") }}> {{$user->name.','.$user->mobile_no}}</option>
                               @endforeach
                               </select>
                          </div>
                        <div class="form-group pr-10">
                            <div class="form-group">
                                <label class="pr-10" for="input">{{ __('Current Balance: ')}}</label>
                                <input type="text" id="current_balance" onchange="validateBalance()" readonly value="{{ old('current_balance') }}" name="current_balance" class="form-control"> </textarea>
                            </div>
                        </div>
                        <div class="form-group pr-10 pr-5">
                            <label for="accounts">Type: &nbsp;</label>
                            <select id="accounts" required name="accounts" onchange="validateBalance()" class="form-control" style="width: 165px">
                              <option value="" selected disabled>Select Type</option>
                              <option  {{ (old("accounts") == 'CREDIT' ? "selected":"") }} value="CREDIT"> Credit</option>
                              <option {{ (old("accounts") == 'DEBIT' ? "selected":"") }} value="DEBIT"> Debit</option>
                            </select>
                          </div>
                          <div class="form-group pr-10">
                            <div class="form-group">
                                <label class="pr-10" for="input">{{ __('Balance: ')}}</label>
                                <input type="text" id="balance" onchange="validateBalance()" value="{{ old('balance') }}" name="balance" class="form-control"> </textarea>
                            </div>
                        </div>  
                        <div class="form-group pr-10 pt-10">
                            <div class="form-group">
                                <label class="pr-10" for="input">{{ __('Validity: ')}}</label>
                                <input type="date" required id="validity" onchange="TDate()"  value="{{ old('validity') }}" name="validity" class="form-control"> </textarea>
                            </div>
                        </div>  
                        <div class="form-group text-center pt-10">
                            <button type="submit" style="height: 35px" class="btn btn-primary mr-10">{{ __('Submit')}}</button>
                        </div>
                        
                     </form>
                </div>
            </div>
        </div>
    </div>  
</div>
    <!-- end file upload here -->

    
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

<script type=text/javascript>
    $('#users').change(function(){
    var user_id = $(this).val();  
    if(user_id){
      $.ajax({
        type:"GET",
        url:"{{url('rcs-users-ajax')}}?user_id="+user_id,
        success:function(res){        
        if(res){
          console.log(res.credit_remaining)
          $("#current_balance").val(res.credit_remaining);
        
        }else{
          $("#current_balance").empty();
        }
        }
      });
    }else{
      $("#current_balance").empty(); 
    }   
    });

    function TDate() {
        var UserDate = document.getElementById("validity").value;
        var ToDate = new Date();

        if (new Date(UserDate).getTime() <= ToDate.getTime()) {
            alert("The Date must be Bigger or Equal to today date");
            return false;
        }
        return true;
    }

    function validateBalance() {
        var accounts = $('#accounts').val();
        var currentBalance = $('#current_balance').val();
        var balance = $('#balance').val();

        if(accounts=='DEBIT' && parseInt(currentBalance)<parseInt(balance)){
            alert("Debited balance must not be small then current balance")
            $('#balance').val('');
        }
        
        return true;
    }



    </script>
 @endpush
@endsection