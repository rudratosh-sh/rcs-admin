<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RcsBalance;
use App\RcsAccount;
use App\User;
use Auth;
use DataTables;
class RcsBalanceController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function index(){
        $data['users'] = User::whereNotIn('id',[Auth::id(),1,2])->get();
        return view('balance.index')->with($data);
    }

    public function getBalanceByUser(Request $request)
	{   
		$balance = RcsBalance::select('credit_remaining')->where('user_id',$request->user_id)->first();
		return response()->json($balance);
	}

    /**
     * RCS Balance editor
     *
     * @return void
     */
    public function store(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'current_balance' => 'required|integer',
            'accounts' => 'required',
            'balance' => "required|integer",
            'validity' => 'required'
        ], [
            'user.required' => 'Please Select User',
            'current_balance.required' => 'Current Balance is Tempred',
            'current_balance.integer' => 'Current Balance Must be Number',
            'accounts.required' => 'Type Must be Selected',
            'balance.required' => 'Balance is Required',
            'balance.integer' => "Balance must be Number",
            'validity.required' => "Validity Must be Selected"
        ]);

        try {
            $accounts = array(
                'user_id' => $request->user,
                'type' => $request->accounts,
                'balance' => $request->balance,
                'validity' => $request->validity,
                'created_by' => Auth::user()->id
            );

            if(!RcsAccount::create($accounts))
                return redirect()->back()->withErrors(["Something Went Wrong"])->withInput($request->all());
            
            $current_rcs_balance = RcsBalance::where('user_id',$request->user)->first();
            if($request->accounts == 'DEBIT' && $current_rcs_balance->credit_remaining - $request->balance < 0)
                return redirect()->back()->withErrors(["Balance must not be smaller then Current Balance"])->withInput($request->all());

            if($request->accounts == 'DEBIT')
                $data = array(
                    'recharge' => $current_rcs_balance->recharge - $request->balance,
                    'credit_remaining' => $current_rcs_balance->credit_remaining - $request->balance,
                    'valid_till' => $request->validity
                );
            elseif($request->accounts == 'CREDIT')
                $data = array(
                    'recharge' => $current_rcs_balance->recharge + $request->balance,
                    'credit_remaining' => $current_rcs_balance->credit_remaining + $request->balance,
                    'valid_till' => $request->validity
                );
            else
                return redirect()->back()->withErrors(["Something Went Wrong"])->withInput($request->all());         
 
            if (RcsBalance::where('user_id',$request->user)->update($data))
                return redirect()->back()->with('success', 'Balance is '.$request->accounts.'ED');
            else
                return redirect()->back()->with('error', 'Something went wrong! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return redirect()->back()->with('error', $bug);
        }
    }

    public function report()
    {
        $data = RcsAccount::getAccounts();
        return Datatables::of($data)
                ->addColumn('managed_by', function($data){
                    $managed_by = $data->created_by;
                    $user = User::where('id',$managed_by)->first();
                        $badges = '<span class="badge badge-dark m-1">'.$user->name.'</span>';
                    return $badges;
                })
                ->rawColumns(['managed_by'])
                    ->addIndexColumn()
                    ->make(true);
    }
}
