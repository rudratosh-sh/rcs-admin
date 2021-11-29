<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\SmsTransactionGroup;
use App\SmsTransactionSingle;
use App\Helpers;
use DataTables, Auth;

class SmartMessageBasicController extends Controller
{
    protected $suggetions;
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
        $this->suggetions = [];
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
        $validator = Validator::make($request->all(), [
            'message_type' => 'required ',
            'message'     => 'required',
            'mobile_no' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        $image_path = NULL;
        if ($request->file()) {
            $request->validate([
                'file' => 'required|mimes:png,jpg,jpeg,gif|max:4096'
            ]);
            $name = time() . '_' . $request->file->getClientOriginalName();
            $image_path = $request->file('file')->storeAs('', str_replace(' ', '', $name));
        }

        try {
            // store sms transaction group
            if ($this->storeSmsTranscationGroup($request, $image_path))
                return redirect('campaiging-report')->with('success', 'Messages added in Queue');
            else
                return redirect('campaiging-report')->with('error', 'Failed to create sms queue! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }


    function storeSmsTranscationGroup(Request $request, $image_path = null)
    {
        $mobile_nos = array_map('intval', explode(',', $request->mobile_no));
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

            //setting image content and title to object    
            if ($pendingGroupSms->image != null && $pendingGroupSms->image != '') {
                $this->media = [
                    'height' => 'TALL',
                    'contentInfo' => ['fileUrl' => url('/uploads/' . $pendingGroupSms->image), 'forceRefresh' => true]
                ];
            }

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
            if ($pendingGroupSms->open_url_title_2 != '' && $pendingGroupSms->open_url_2 = '') {
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
            //pushing suggestions to object
            if ($this->dialCall != null)
                $this->suggetions[] = $this->dialCall;
            if ($this->callUrl1 != null)
                $this->suggetions[] = $this->callUrl1;
            if ($this->callUrl2 != null)
                $this->suggetions[] = $this->callUrl2;
            if ($this->callUrl3 != null)
                $this->suggetions[] = $this->callUrl3;

            //pushing text to Content Object
            $this->cardContent = array(
                'title' => $this->text,
                'description' => $this->description,
                'media' => $this->media,
                'suggestions' => $this->suggetions
            );
        }
    }
}