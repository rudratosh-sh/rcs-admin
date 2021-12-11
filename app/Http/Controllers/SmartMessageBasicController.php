<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\SmsTransactionGroup;
use App\SmsTransactionSingle;
use App\Helpers;
use DataTables, Auth;
use App\RcsBalance;

class SmartMessageBasicController extends Controller
{
    protected $suggestions;
    protected $text;
    protected $media;
    protected $cardContent;
    protected $dialCall;
    protected $callUrl1;
    protected $callUrl2;
    protected $callUrl3;
    protected $thumbnailImageAlignment;
    protected $cardOrientation;
    protected $standaloneCard;
    protected $richCard;
    protected $contentMessage;
    protected $description;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cardContent = [];
        $this->dialCall = [];
        $this->suggestions = [];
        $this->media = [];
        $this->thumbnailImageAlignment = 'RIGHT';
        $this->cardOrientation = 'VERTICAL';

        $this->middleware('auth', ['except' => 'sendBulkBasicSms']);
    }

    public function index()
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
        //check if rcs key available for current user
        if (!file_exists(public_path('rcs_keys/' . Auth::user()->id . ".json")))
            return redirect()->back()->withErrors(["RCS API Key is not Available. Contact to Administrator"])->withInput($request->all());
       
        $request->validate([
            'message_type' => 'required',
            'message' => 'required',
            'mobile_no' => 'required'
        ], [
            'message_type.required' => 'Message Type is Required',
            'message.required' => 'Message is Required',
            'mobile_no.required' => 'Mobile No is Required',
        ]);

        $image_path = NULL;
        if ($request->file()) {
            $request->validate([
                'file' => 'required|mimes:png,jpg,jpeg,gif|max:4096'
            ]);
            $name = time() . '_' . $request->file->getClientOriginalName();
            $image_path = $request->file('file')->storeAs('', str_replace(' ', '', $name));
        }

        try {
            //get mobile counts
            $mobile_nos = array_map('intval', explode(',', $request->mobile_no));
            //check if user has enough balance
            if (!$this->getBalance(count($mobile_nos)))
                return redirect()->back()->withErrors(["Don't Have Enough Credit to Spend"])->withInput($request->all());

            // store sms transaction group
            if ($this->storeSmsTranscationGroup($request, $image_path, $mobile_nos))
                return redirect('campaiging-report')->with('success', 'Messages added in Queue');
            else
                return redirect('campaiging-report')->with('error', 'Failed to create sms queue! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return redirect()->back()->with('error', $bug);
        }
    }


    function storeSmsTranscationGroup(Request $request, $image_path = null, $mobile_nos = [])
    {
        try {
            // store sms transaction group
            $storeSmsTransaction = SmsTransactionGroup::create([
                'user_id' => Auth::user()->id,
                'message_form' => 'BASIC',
                'image' => $image_path,
                'message_type' => $request->message_type,
                'sms_count' => count($mobile_nos),
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
                'created_at' => date("Y-m-d H:i:s")
            ]);

            //Storing single transaction data
            if ($storeSmsTransaction)
                return $this->storeSmsTranscationSingle($request, $image_path);
            else
                return false;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    // store sms transaction single
    function storeSmsTranscationSingle(Request $request, $image_path)
    {
        try {
            $mobile_nos = senitizeMobileNumbers($request->mobile_no);
            $group_id = SmsTransactionGroup::latest()->first()->id;

            $data  = [];

            if (!empty($mobile_nos)) {
                $data = array_map(function ($arr) use ($group_id) {
                    return array_merge(['mobile_no' => $arr], ['sms_transaction_group_id' => $group_id], ['status' => 0]);
                }, $mobile_nos);
            }

            if (!empty($data)) {
                if (SmsTransactionSingle::insert($data))
                    return true;
                else
                    return false;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Send Smart Message Bulk
     *
     * @return void
     */

    public function sendBulkBasicSms()
    {
        $pendingGroupSms = SmsTransactionGroup::where('status', 0)->first();
        //checking if data exist with pending sms
        if (!empty($pendingGroupSms->id)) {
            $pendingSingleSms = SmsTransactionSingle::where('sms_transaction_group_id', $pendingGroupSms->id)
                ->where('status', 0)->get();

            $this->prepareBasicSmsRaw($pendingGroupSms);
            //creating standalone card
            $this->standaloneCard = array(
                'standaloneCard' => array(
                    'thumbnailImageAlignment' => $this->thumbnailImageAlignment,
                    'cardOrientation' => $this->cardOrientation,
                    'cardContent' => $this->cardContent
                )
            );
            //creating rich card
            $this->richCard = ['richCard' => $this->standaloneCard];
            //creating contentMessage object
            $this->contentMessage = ['contentMessage' => $this->richCard];
            $job = (new \App\Jobs\SendBulkBasicSms($this->contentMessage, $pendingSingleSms, $pendingGroupSms->user_id))
                ->delay(
                    now()
                        ->addSeconds(2)
                );

            dispatch($job);
            echo "Bulk mail send successfully in the background...";
        }
    }

    private function prepareBasicSmsRaw($pendingGroupSms)
    {
        if (!empty($pendingGroupSms)) {
            //setting text message in object
            if ($pendingGroupSms->message != '')
                $this->text = $pendingGroupSms->image_title;
            $this->description = $pendingGroupSms->message;

            //setting call url to object
            if ($pendingGroupSms->call_title != '' && $pendingGroupSms->call_number != '') {
                $this->dialCall = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->call_title,
                        'postbackData' => 'postback_data_1234',
                        'fallbackUrl' => 'https://www.google.com/contact/',
                        'dialAction' => ['phoneNumber' => $pendingGroupSms->call_number],
                    ]
                );
            }

            //setting open url 1 to object
            if ($pendingGroupSms->open_url_title_1 != '' && $pendingGroupSms->open_url_1 != '') {
                $this->callUrl1 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_1,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_1]
                    ]
                );
            }

            //setting open url 2 to object
            if ($pendingGroupSms->open_url_title_2 != '' && $pendingGroupSms->open_url_2 != '') {
                $this->callUrl2 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_2,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_2]
                    ]
                );
            }

            //setting open url 3 to object
            if ($pendingGroupSms->open_url_title_3 != '' && $pendingGroupSms->open_url_3 != '') {
                $this->callUrl3 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_3,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_3]
                    ]
                );
            }
            //setting image content and title to object    
            if ($pendingGroupSms->image != null && $pendingGroupSms->image != '') {
                $imageSize = !empty($this->dialCall)  + !empty($this->callUrl1)  + !empty($this->callUrl2)  + !empty($this->callUrl3);
                if ($imageSize == 0)
                    $cardWidth = 'TALL';
                elseif ($imageSize == 1)
                    $cardWidth = 'MEDIUM';
                else
                    $cardWidth = 'SHORT';

                $this->media = [
                    'height' => 'TALL',
                    'contentInfo' => ['fileUrl' => url('/uploads/' . $pendingGroupSms->image), 'forceRefresh' => true]
                ];
            }
            //pushing suggestions to object
            if ($this->dialCall != null)
                $this->suggestions[] = $this->dialCall;
            if ($this->callUrl1 != null)
                $this->suggestions[] = $this->callUrl1;
            if ($this->callUrl2 != null)
                $this->suggestions[] = $this->callUrl2;
            if ($this->callUrl3 != null)
                $this->suggestions[] = $this->callUrl3;

            //pushing text to Content Object
            if($this->text!='')
                $this->cardContent = array_merge($this->cardContent,['title'=>$this->text]);
            if($this->description!='')
                $this->cardContent = array_merge($this->cardContent,['description'=>$this->description]);
            if(!empty($this->media))
                $this->cardContent = array_merge($this->cardContent,['media'=>$this->media]);
            if(!empty($this->suggestions))
                $this->cardContent = array_merge($this->cardContent,['suggestions'=>$this->suggestions]);
        }
    }

    private function getBalance($nos = 0)
    {
        if (Auth::user()->id <= 2)
            return true;
        $balance =  getBalance(Auth::user()->id);
        $creditRemaining = $balance['creditRemaining'];
        $lastRecharged = $balance['lastRecharged'];
        if ($creditRemaining < $nos)
            return false;
        else
            return RcsBalance::where('id', $lastRecharged->id)->update(
                array(
                    'credit_remaining' => $lastRecharged->credit_remaining - $nos,
                    'credit_spend' => $lastRecharged->credit_spend + $nos
                )
            );
    }
}
