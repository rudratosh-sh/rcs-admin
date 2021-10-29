<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\SmsTransactionGroup;
use App\SmsTransactionSingle;
use App\Helpers;

use DataTables, Auth;

class SmartMessageController extends Controller
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

    public function basic()
    {   
        return view('smart-messages/basic');
    }

    /**
     * Send Smart Message Basic
     *
     * @return void
     */
    public function sendSmartMessageBasic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message_type' => 'required ',
            'message'     => 'required',
            'mobile_no' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        //image upload
        $image_path = NULL;
        if($request->file()) {
            $request->validate([
                'file' => 'required|mimes:png,jpg,jpeg|max:4096'
            ]); 
            $name = time().'_'.$request->file->getClientOriginalName();
            $image_path = $request->file('file')->storeAs('', $name);
        }    
        try {
            // store sms transaction group
            $this->storeSmsTranscationGroup($request,$image_path);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    
    function storeSmsTranscationGroup(Request $request,$image_path=null)
    {
        try {
            // store sms transaction group
            $storeSmsTransaction = SmsTransactionGroup::create([
                'user_id' => Auth::user()->id,
                'message_form' => 'BASIC',
                'image' => $image_path,
                'message_type' => $request->message_type,
                'message' => $request->message,
                'image_title' => $request->image_title ? $request->image_title : null,
                'call_title' => $request->call_title ? $request->call_title : null,
                'call_number' => $request->call_number ? $request->call_number : null,
                'open_url_title_1' => $request->open_url_title_1 ? $request->open_url_title_1 : null,
                'open_url_1' => $request->open_url_1 ? $request->open_url_1 : null,
                'open_url_title_2' => $request->open_url_title_2 ? $request->open_url_title_2 : null,
                'open_url_2' => $request->open_url_2 ? $request->open_url_2 : null,
                'open_url_title_3' => $request->open_url_title_3 ? $request->open_url_title_3 : null,
                'open_url_3' => $request->open_url_3 ? $request->open_url_3 : null,
            ]);
            //Storing single transaction data
            $this->storeSmsTranscationSingle($request);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    // store sms transaction single
    function storeSmsTranscationSingle(Request $request)
    {
        try {
            $mobile_nos = array_map('intval', explode(',', $request->mobile_no));
            $data  = [];
            if (!empty($mobile_nos)){
                foreach ($mobile_nos as $key => $mobile) :
                    $mobile = '+'.$mobile;
                    $sendMessage = callRcsSendTextMessage($mobile,Auth::user()->id,$request->message);
                    if($sendMessage['status_code']==200)
                        $status = 1;
                    else
                        $status = 3;    
                    $data[$key] = [
                        'sms_transaction_group_id' => SmsTransactionGroup::latest()->first()->id,
                        'mobile_no' => $mobile,
                        'status' => $status,
                        'response' => $sendMessage['response']
                    ];
                endforeach;
            }
            if(!empty($data)){
                if(SmsTransactionSingle::insert($data)) 
                    $this->redirectVargin();  
                else
                    return redirect()->back()->withInput()->with('error', 'Something Went Wrong');
            }    
            else{
                return redirect()->back()->withInput()->with('error', 'Something Went Wrong');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    function redirectVargin(){
        return redirect()->route('campaiging-report')->with('message','Message Added to Queue Successfully'); 
    }
}
