<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\SmsTransactionSingle;
use App\SmsTransactionSingleAdvance;
use DataTables,Auth;

class ReportsController extends Controller
{
     /**
    * Create a new controller instance.
    *
    * @return void
    */
   public function __construct()
   { 
       $this->middleware('auth');
   }

   public function smartReport(){
        return view('reports.smart-report');
   }
   public function smartReportData()
    {
        $basic = SmsTransactionSingle::getCombinedDataBasic(1);
        $advance = SmsTransactionSingleAdvance::getCombinedDataAdvance(1);
        $basic = json_decode(json_encode($basic), true);
        $advance = json_decode(json_encode($advance), true);

        //adding type in array
        $basic = array_map(function($arr){
            return $arr + ['smart_type' => 'Basic'];
        }, $basic);

        $advance = array_map(function($arr){
            return $arr + ['smart_type' => 'Advance'];
        }, $advance);

        $data = array_merge($basic,$advance);

        return Datatables::of($data)
                // ->rawColumns(['roles','permissions','action'])
                    ->addIndexColumn()
                    ->make(true);
    }
}
