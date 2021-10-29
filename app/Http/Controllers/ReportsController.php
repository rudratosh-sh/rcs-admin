<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

   public function campaigingReport()
    {
        return view('reports/campaiging-report');
    }
}
