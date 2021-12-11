<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RcsBalance;
use App\RcsAccount;
use App\User;
use Auth;
class RcsBalanceController extends Controller
{
    public function index(){
        $data['users'] = User::whereNotIn('id',[Auth::id()])->get();
        return view('balance.index')->with($data);
    }

    public function getBalanceByUser(Request $request)
	{   
		$balance = RcsBalance::select('credit_remaining')->where('user_id',$request->user_id)->first();
		return response()->json($balance);
	}
}
