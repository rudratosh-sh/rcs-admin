<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\SmsTransactionGroupAdvance;
use App\SmsTransactionSingleAdvance;
use App\Helpers;
use DataTables, Auth;

class SmartMessageAdvanceController extends Controller
{
    protected $cardWidth;
    protected $standaloneCard;
    protected $richCard;
    protected $contentMessage;
    protected $cardContentBulk;
    /* Card 1 Config */
    protected $suggetionsCard1;
    protected $descriptionCard1;
    protected $textCard1;
    protected $mediaCard1;
    protected $dialCallCard1;
    protected $callUrl1Card1;
    protected $callUrl2Card1;
    protected $callUrl3Card1;
    protected $cardContentCard1;
    /* Card 2 Config */
    protected $suggetionsCard2;
    protected $descriptionCard2;
    protected $textCard2;
    protected $mediaCard2;
    protected $dialCallCard2;
    protected $callUrl1Card2;
    protected $callUrl2Card2;
    protected $callUrl3Card2;
    protected $cardContentCard2;
    /* Card 3 Config */
    protected $suggetionsCard3;
    protected $descriptionCard3;
    protected $textCard3;
    protected $mediaCard3;
    protected $dialCallCard3;
    protected $callUrl1Card3;
    protected $callUrl2Card3;
    protected $callUrl3Card3;
    protected $cardContentCard3;
    /* Card 4 Config */
    protected $suggetionsCard4;
    protected $descriptionCard4;
    protected $textCard4;
    protected $mediaCard4;
    protected $dialCallCard4;
    protected $callUrl1Card4;
    protected $callUrl2Card4;
    protected $callUrl3Card4;
    protected $cardContentCard4;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cardWidth = 'MEDIUM';
        /*Card 1 */
        $this->dialCallCard1 = [];
        $this->suggetionsCard1 = [];
        $this->mediaCard1 = [];
        $this->cardContentCard1 = [];
        /*Card 2 */
        $this->dialCallCard2 = [];
        $this->suggetionsCard2 = [];
        $this->mediaCard2 = [];
        $this->cardContentCard2 = [];
        $this->callUrl2Card2 = null;
        /*Card 3 */
        $this->dialCallCard3 = [];
        $this->suggetionsCard3 = [];
        $this->mediaCard3 = [];
        $this->cardContentCard3 = [];
        /*Card 4 */
        $this->dialCallCard4 = [];
        $this->suggetionsCard4 = [];
        $this->mediaCard4 = [];
        $this->cardContentCard4 = [];

        $this->middleware('auth');
    }

    public function index()
    {
        return view('smart-messages/advance');
    }

    /**
     * Send Smart Message Advance
     *
     * @return void
     */
    public function sendSmartMessageAdvance(Request $request)
    {
        $request->validate([
            'message_type' => 'required ',
            'mobile_no' => 'required'
        ]);

        //validate & upload images for cards
        $imagesCardsArr = $this->storeImages($request);

        try {
            // store sms transaction group advance
            if ($this->storeSmsTranscationGroup($request, $imagesCardsArr))
                return redirect('campaiging-report')->with('success', 'Messages added in Queue');
            else
                return redirect('campaiging-report')->with('error', 'Failed to create sms queue! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store SMS trasaction group for advance
     *
     * @return void
     */
    function storeSmsTranscationGroup(Request $request, $imagesCardsArr = [])
    {
        $mobile_nos = array_map('intval', explode(',', $request->mobile_no));
        try {
            //get prepared post data 
            $data = $this->prepareStoreData($request, $imagesCardsArr, $mobile_nos);
            // store sms transaction group
            $storeSmsTransaction = SmsTransactionGroupAdvance::create($data);

            //Storing single transaction data
            if ($storeSmsTransaction)
                return $this->storeSmsTranscationSingle($request, $imagesCardsArr);
            else
                return false;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }


    /**
     * Store sms transaction single
     *
     * @return void
     */
    function storeSmsTranscationSingle(Request $request, $image_path)
    {
        try {
            $mobile_nos = array_map('intval', explode(',', $request->mobile_no));
            $mobile_nos = array_map(function ($val) {
                return '+' . $val;
            }, $mobile_nos);
            $group_id = SmsTransactionGroupAdvance::latest()->first()->id;

            $data  = [];
            if (!empty($mobile_nos)) {
                $data = array_map(function ($arr) use ($group_id) {
                    return array_merge(['mobile_no' => $arr], ['sms_transaction_group_advance_id' => $group_id], ['status' => 0]);
                }, $mobile_nos);
            }

            if (!empty($data)) {
                if (SmsTransactionSingleAdvance::insert($data))
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
     * Send Smart Message Bulk Advance
     *
     * @return void
     */
    public function sendBulkAdvanceSms()
    {
        $pendingGroupSms = SmsTransactionGroupAdvance::where('status', 0)->first();
        //checking if data exist with pending sms
        if (!empty($pendingGroupSms->id)) {
            $pendingSingleSms = SmsTransactionSingleAdvance::where('sms_transaction_group_advance_id', $pendingGroupSms->id)
                ->where('status', 0)->get();

            $this->prepareContentData($pendingGroupSms);  
            //creating standalone card
            $this->standaloneCard = array(
                'carouselCard' => array(
                    'cardWidth' => 'MEDIUM',
                    'cardContents' => $this->cardContentBulk
                )
            );
            //creating rich card
            $this->richCard = ['richCard' => $this->standaloneCard];

            //creating contentMessage object
            $this->contentMessage = ['contentMessage' => $this->richCard];
            $job = (new \App\Jobs\SendBulkAdvanceSms($this->contentMessage, $pendingSingleSms, $pendingGroupSms->user_id))
                ->delay(
                    now()
                        ->addSeconds(2)
                );

            dispatch($job);
            echo "Bulk mail send successfully in the background...";
        }
    }


    /** Prepare Content data 
     */
    public function prepareContentData($bulkData)
    {
        $this->cardContentBulk = array(
            $this->prepareAdvanceSmsRawCard1($bulkData),
            $this->prepareAdvanceSmsRawCard2($bulkData),
            $this->prepareAdvanceSmsRawCard3($bulkData),
            $this->prepareAdvanceSmsRawCard4($bulkData)
        );
    }
    /** 
     * Preapre data for card 1
     * @return void
     * */
    private function prepareAdvanceSmsRawCard1($pendingGroupSms)
    {
        if (!empty($pendingGroupSms)) {
            //setting text message in object
            if ($pendingGroupSms->message_card_1 != '')
                $this->textCard1 = $pendingGroupSms->image_title_card_1;
            $this->descriptionCard1 = $pendingGroupSms->message_card_1;

            //setting call url to object
            if ($pendingGroupSms->call_title_card_1 != '' && $pendingGroupSms->call_number_card_1 != '') {
                $this->dialCallCard1 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->call_title_card_1,
                        'postbackData' => 'postback_data_1234',
                        'fallbackUrl' => 'https://www.google.com/contact/',
                        'dialAction' => ['phoneNumber' => $pendingGroupSms->call_number_card_1],
                    ]
                );
            } else {
                $this->dialCallCard1 = null;
            }

            //setting open url 1 to object
            if ($pendingGroupSms->open_url_title_1_card_1 != '' && $pendingGroupSms->open_url_1_card_1 != '') {
                $this->callUrl1Card1 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_1_card_1,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_1_card_1]
                    ]
                );
            } else {
                $this->callUrl1Card1 = null;
            }

            //setting open url 2 to object
            if ($pendingGroupSms->open_url_title_2_card_1 != '' && $pendingGroupSms->open_url_2_card_1 != '') {
                $this->callUrl2Card1 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_2_card_1,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_2_card_1]
                    ]
                );
            } else {
                $this->callUrl2Card1 = null;
            }

            //setting open url 3 to object
            if ($pendingGroupSms->open_url_title_3_card_1 != '' && $pendingGroupSms->open_url_3_card_1 != '') {
                $this->callUrl3Card1 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_3_card_1,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_3_card_1]
                    ]
                );
            } else {
                $this->callUrl3Card1 = null;
            }

            //setting media height
            $imageSize = !empty($this->dialCallCard1)  + !empty($this->callUrl1Card1)  + !empty($this->callUrl2Card1)  + !empty($this->callUrl3Card1);
            if ($imageSize == 0)
                $this->cardWidth = 'TALL';
            elseif ($imageSize == 1)
                $this->cardWidth = 'MEDIUM';
            else
                $this->cardWidth = 'SHORT';

            //setting image content and title to object    
            if ($pendingGroupSms->image_card_1 != null && $pendingGroupSms->image_card_1 != '') {
                $this->mediaCard1 = [
                    'height' => $this->cardWidth,
                    'contentInfo' => ['fileUrl' => url('/uploads/' . $pendingGroupSms->image_card_1), 'forceRefresh' => true]
                ];
            } else {
                $this->mediaCard1 = null;
            }

            //pushing suggestions to object
            if ($this->dialCallCard1 != null)
                $this->suggetionsCard1[] = $this->dialCallCard1;
            if ($this->callUrl1Card1 != null)
                $this->suggetionsCard1[] = $this->callUrl1Card1;
            if ($this->callUrl2Card1 != null)
                $this->suggetionsCard1[] = $this->callUrl2Card1;
            if ($this->callUrl3Card1 != null)
                $this->suggetionsCard1[] = $this->callUrl3Card1;

            //pushing text to Content Object
            return $this->cardContentCard1 = array(
                'title' => $this->textCard1,
                'description' => $this->descriptionCard1,
                'media' => $this->mediaCard1,
                'suggestions' => $this->suggetionsCard1
            );
        }
    }

    /** 
     * Preapre data for card 2
     * @return void
     * */
    private function prepareAdvanceSmsRawcard2($pendingGroupSms)
    {
        if (!empty($pendingGroupSms)) {
            //setting text message in object
            if ($pendingGroupSms->message_card_2 != '')
                $this->textcard2 = $pendingGroupSms->image_title_card_2;
            $this->descriptioncard2 = $pendingGroupSms->message_card_2;

            //setting call url to object
            if ($pendingGroupSms->call_title_card_2 != '' && $pendingGroupSms->call_number_card_2 != '') {
                $this->dialCallcard2 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->call_title_card_2,
                        'postbackData' => 'postback_data_1234',
                        'fallbackUrl' => 'https://www.google.com/contact/',
                        'dialAction' => ['phoneNumber' => $pendingGroupSms->call_number_card_2],
                    ]
                );
            } else {
                $this->dialCallcard2 = null;
            }

            //setting open url 1 to object
            if ($pendingGroupSms->open_url_title_1_card_2 != '' && $pendingGroupSms->open_url_1_card_2 != '') {
                $this->callUrl1card2 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_1_card_2,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_1_card_2]
                    ]
                );
            } else {
                $this->callUrl1card2 = null;
            }

            //setting open url 2 to object
            if ($pendingGroupSms->open_url_title_2_card_2 != '' && $pendingGroupSms->open_url_2_card_2 != '') {
                $this->callUrl2card2 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_2_card_2,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_2_card_2]
                    ]
                );
            } else {
                $this->callUrl2card2 = null;
            }

            //setting open url 3 to object
            if ($pendingGroupSms->open_url_title_3_card_2 != '' && $pendingGroupSms->open_url_3_card_2 != '') {
                $this->callUrl3card2 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_3_card_2,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_3_card_2]
                    ]
                );
            } else {
                $this->callUrl3card2 = null;
            }

            //setting media height
            $imageSize = !empty($this->dialCallcard2)  + !empty($this->callUrl1card2)  + !empty($this->callUrl2card2)  + !empty($this->callUrl3card2);
            if ($imageSize == 0)
                $this->cardWidth = 'TALL';
            elseif ($imageSize == 1)
                $this->cardWidth = 'MEDIUM';
            else
                $this->cardWidth = 'SHORT';

            //setting image content and title to object    
            if ($pendingGroupSms->image_card_2 != null && $pendingGroupSms->image_card_2 != '') {
                $this->mediacard2 = [
                    'height' => $this->cardWidth,
                    'contentInfo' => ['fileUrl' => url('/uploads/' . $pendingGroupSms->image_card_2), 'forceRefresh' => true]
                ];
            } else {
                $this->mediacard2 = null;
            }

            //pushing suggestions to object
            if ($this->dialCallcard2 != null)
                $this->suggetionscard2[] = $this->dialCallcard2;
            if ($this->callUrl1card2 != null)
                $this->suggetionscard2[] = $this->callUrl1card2;
            if ($this->callUrl2card2 != null)
                $this->suggetionscard2[] = $this->callUrl2card2;
            if ($this->callUrl3card2 != null)
                $this->suggetionscard2[] = $this->callUrl3card2;

            //pushing text to Content Object
            return $this->cardContentcard2 = array(
                'title' => $this->textcard2,
                'description' => $this->descriptioncard2,
                'media' => $this->mediacard2,
                'suggestions' => $this->suggetionscard2
            );
        }
    }

    /** 
     * Preapre data for card 3
     * @return void
     * */
    private function prepareAdvanceSmsRawcard3($pendingGroupSms)
    {
        if (!empty($pendingGroupSms)) {
            //setting text message in object
            if ($pendingGroupSms->message_card_3 != '')
                $this->textcard3 = $pendingGroupSms->image_title_card_3;
            $this->descriptioncard3 = $pendingGroupSms->message_card_3;

            //setting call url to object
            if ($pendingGroupSms->call_title_card_3 != '' && $pendingGroupSms->call_number_card_3 != '') {
                $this->dialCallcard3 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->call_title_card_3,
                        'postbackData' => 'postback_data_1234',
                        'fallbackUrl' => 'https://www.google.com/contact/',
                        'dialAction' => ['phoneNumber' => $pendingGroupSms->call_number_card_3],
                    ]
                );
            } else {
                $this->dialCallcard3 = null;
            }

            //setting open url 1 to object
            if ($pendingGroupSms->open_url_title_1_card_3 != '' && $pendingGroupSms->open_url_1_card_3 != '') {
                $this->callUrl1card3 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_1_card_3,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_1_card_3]
                    ]
                );
            } else {
                $this->callUrl1card3 = null;
            }

            //setting open url 2 to object
            if ($pendingGroupSms->open_url_title_2_card_3 != '' && $pendingGroupSms->open_url_2_card_3 != '') {
                $this->callUrl2card3 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_2_card_3,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_2_card_3]
                    ]
                );
            } else {
                $this->callUrl2card3 = null;
            }

            //setting open url 3 to object
            if ($pendingGroupSms->open_url_title_3_card_3 != '' && $pendingGroupSms->open_url_3_card_3 != '') {
                $this->callUrl3card3 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_3_card_3,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_3_card_3]
                    ]
                );
            } else {
                $this->callUrl3card3 = null;
            }

            //setting media height
            $imageSize = !empty($this->dialCallcard3)  + !empty($this->callUrl1card3)  + !empty($this->callUrl2card3)  + !empty($this->callUrl3card3);
            if ($imageSize == 0)
                $this->cardWidth = 'TALL';
            elseif ($imageSize == 1)
                $this->cardWidth = 'MEDIUM';
            else
                $this->cardWidth = 'SHORT';

            //setting image content and title to object    
            if ($pendingGroupSms->image_card_3 != null && $pendingGroupSms->image_card_3 != '') {
                $this->mediacard3 = [
                    'height' => $this->cardWidth,
                    'contentInfo' => ['fileUrl' => url('/uploads/' . $pendingGroupSms->image_card_3), 'forceRefresh' => true]
                ];
            } else {
                $this->mediacard3 = null;
            }

            //pushing suggestions to object
            if ($this->dialCallcard3 != null)
                $this->suggetionscard3[] = $this->dialCallcard3;
            if ($this->callUrl1card3 != null)
                $this->suggetionscard3[] = $this->callUrl1card3;
            if ($this->callUrl2card3 != null)
                $this->suggetionscard3[] = $this->callUrl2card3;
            if ($this->callUrl3card3 != null)
                $this->suggetionscard3[] = $this->callUrl3card3;

            //pushing text to Content Object
            return $this->cardContentcard3 = array(
                'title' => $this->textcard3,
                'description' => $this->descriptioncard3,
                'media' => $this->mediacard3,
                'suggestions' => $this->suggetionscard3
            );
        }
    }

    /** 
     * Preapre data for card 1
     * @return void
     * */
    private function prepareAdvanceSmsRawcard4($pendingGroupSms)
    {
        if (!empty($pendingGroupSms)) {
            //setting text message in object
            if ($pendingGroupSms->message_card_4 != '')
                $this->textcard4 = $pendingGroupSms->image_title_card_4;
            $this->descriptioncard4 = $pendingGroupSms->message_card_4;

            //setting call url to object
            if ($pendingGroupSms->call_title_card_4 != '' && $pendingGroupSms->call_number_card_4 != '') {
                $this->dialCallcard4 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->call_title_card_4,
                        'postbackData' => 'postback_data_1234',
                        'fallbackUrl' => 'https://www.google.com/contact/',
                        'dialAction' => ['phoneNumber' => $pendingGroupSms->call_number_card_4],
                    ]
                );
            } else {
                $this->dialCallcard4 = null;
            }

            //setting open url 1 to object
            if ($pendingGroupSms->open_url_title_1_card_4 != '' && $pendingGroupSms->open_url_1_card_4 != '') {
                $this->callUrl1card4 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_1_card_4,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_1_card_4]
                    ]
                );
            } else {
                $this->callUrl1card4 = null;
            }

            //setting open url 2 to object
            if ($pendingGroupSms->open_url_title_2_card_4 != '' && $pendingGroupSms->open_url_2_card_4 != '') {
                $this->callUrl2card4 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_2_card_4,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_2_card_4]
                    ]
                );
            } else {
                $this->callUrl2card4 = null;
            }

            //setting open url 3 to object
            if ($pendingGroupSms->open_url_title_3_card_4 != '' && $pendingGroupSms->open_url_3_card_4 != '') {
                $this->callUrl3card4 = array(
                    'action' =>
                    [
                        'text' => $pendingGroupSms->open_url_title_3_card_4,
                        'postbackData' => 'postback_data_1234',
                        'openUrlAction' => ['url' => $pendingGroupSms->open_url_3_card_4]
                    ]
                );
            } else {
                $this->callUrl3card4 = null;
            }

            //setting media height
            $imageSize = !empty($this->dialCallcard4)  + !empty($this->callUrl1card4)  + !empty($this->callUrl2card4)  + !empty($this->callUrl3card4);
            if ($imageSize == 0)
                $this->cardWidth = 'TALL';
            elseif ($imageSize == 1)
                $this->cardWidth = 'MEDIUM';
            else
                $this->cardWidth = 'SHORT';

            //setting image content and title to object    
            if ($pendingGroupSms->image_card_4 != null && $pendingGroupSms->image_card_4 != '') {
                $this->mediacard4 = [
                    'height' => $this->cardWidth,
                    'contentInfo' => ['fileUrl' => url('/uploads/' . $pendingGroupSms->image_card_4), 'forceRefresh' => true]
                ];
            } else {
                $this->mediacard4 = null;
            }

            //pushing suggestions to object
            if ($this->dialCallcard4 != null)
                $this->suggetionscard4[] = $this->dialCallcard4;
            if ($this->callUrl1card4 != null)
                $this->suggetionscard4[] = $this->callUrl1card4;
            if ($this->callUrl2card4 != null)
                $this->suggetionscard4[] = $this->callUrl2card4;
            if ($this->callUrl3card4 != null)
                $this->suggetionscard4[] = $this->callUrl3card4;

            //pushing text to Content Object
            return $this->cardContentcard4 = array(
                'title' => $this->textcard4,
                'description' => $this->descriptioncard4,
                'media' => $this->mediacard4,
                'suggestions' => $this->suggetionscard4
            );
        }
    }
    /**
     * Store Images for all 4 Cards
     */
    public function storeImages($request)
    {
        if ($request->card_1_check) {
            $request->validate([
                'file_card_1' => 'required|mimes:png,jpg,jpeg,gif|max:4096',
                'message_card_1' => 'required'
            ], [
                'file_card_1.required' => 'Image Required for Card 1',
                'file_card_1.mimes' => 'Only Image File Allowed',
                'file_card_1.max' => 'Only Image 4 MB File Allowed',
                'message_card_1.required' => 'Message Required for Card 1',
            ]);
        }

        if ($request->card_2_check) {
            $request->validate([
                'file_card_2' => 'required|mimes:png,jpg,jpeg,gif|max:4096',
                'message_card_2' => 'required'
            ], [
                'file_card_2.required' => 'Image Required for Card 2',
                'file_card_2.mimes' => 'Only Image File Allowed',
                'file_card_2.max' => 'Only Image 4 MB File Allowed',
                'message_card_2.required' => 'Message Required for Card 2',
            ]);
        }

        if ($request->card_3_check) {
            $request->validate([
                'file_card_3' => 'required|mimes:png,jpg,jpeg,gif|max:4096',
                'message_card_3' => 'required'
            ], [
                'file_card_3.required' => 'Image Required for Card 3',
                'file_card_3.mimes' => 'Only Image File Allowed',
                'file_card_3.max' => 'Only Image 4 MB File Allowed',
                'message_card_3.required' => 'Message Required for Card 3',
            ]);
        }

        if ($request->card_4_check) {
            $request->validate([
                'file_card_4' => 'required|mimes:png,jpg,jpeg,gif|max:4096',
                'message_card_4' => 'required'
            ], [
                'file_card_4.required' => 'Image Required for Card 4',
                'file_card_4.mimes' => 'Only Image File Allowed',
                'file_card_4.max' => 'Only Image 4 MB File Allowed',
                'message_card_4.required' => 'Message Required for Card 4',
            ]);
        }

        $imageArr = [];
        //upload image for card 1
        if ($request->hasFile('file_card_1')) {
            $tempName = time() . '_' . $request->file_card_1->getClientOriginalName();
            $imagePath = $request->file('file_card_1')->storeAs('', str_replace(' ', '', $tempName));
            $imageArr['imageCard1'] = $imagePath;
        }

        //upload image for card 2
        if ($request->hasFile('file_card_2')) {
            $tempName = time() . '_' . $request->file_card_2->getClientOriginalName();
            $imagePath = $request->file('file_card_2')->storeAs('', str_replace(' ', '', $tempName));
            $imageArr['imageCard2'] = $imagePath;
        }

        //upload image for card 3
        if ($request->hasFile('file_card_3')) {
            $tempName = time() . '_' . $request->file_card_3->getClientOriginalName();
            $imagePath = $request->file('file_card_3')->storeAs('', str_replace(' ', '', $tempName));
            $imageArr['imageCard3'] = $imagePath;
        }

        //upload image for card 4
        if ($request->hasFile('file_card_4')) {
            $tempName = time() . '_' . $request->file_card_4->getClientOriginalName();
            $imagePath = $request->file('file_card_4')->storeAs('', str_replace(' ', '', $tempName));
            $imageArr['imageCard4'] = $imagePath;
        }

        return $imageArr;
    }

    /**
     * prepare data for stroing in sms transaction advance group
     */
    public function prepareStoreData($request, $imagesCardsArr = [], $mobile_nos = null)
    {
        return array(
            'user_id' => Auth::user()->id,
            'message_form' => 'ADVANCE',
            'message_type' => $request->message_type,
            'sms_count' => count($mobile_nos),
            'message_card_1' => $request->message_card_1,
            'image_card_1' => $imagesCardsArr['imageCard1'] ? $imagesCardsArr['imageCard1'] : null,
            'image_title_card_1' => $request->image_title_card_1 ? $request->image_title_card_1 : null,
            'call_title_card_1' => $request->call_title_card_1 ? $request->call_title_card_1 : null,
            'call_number_card_1' => $request->call_number_card_1 ? $request->call_number_card_1 : null,
            'open_url_title_1_card_1' => $request->open_url_title_1_card_1 ? $request->open_url_title_1_card_1 : null,
            'open_url_1_card_1' => $request->open_url_1_card_1 ? $request->open_url_1_card_1 : null,
            'open_url_title_2_card_1' => $request->open_url_title_2_card_1 ? $request->open_url_title_2_card_1 : null,
            'open_url_2_card_1' => $request->open_url_2_card_1 ? $request->open_url_2_card_1 : null,
            'open_url_title_3_card_1' => $request->open_url_title_3_card_1 ? $request->open_url_title_3_card_1 : null,
            'open_url_3_card_1' => $request->open_url_3_card_1 ? $request->open_url_3_card_1 : null,
            'message_length_card_1' => $request->message_card_1 ? strlen($request->message_card_1) : null,
            'message_card_2' => $request->message_card_2,
            'image_card_2' => $imagesCardsArr['imageCard1'] ? $imagesCardsArr['imageCard1'] : null,
            'image_title_card_2' => $request->image_title_card_2 ? $request->image_title_card_2 : null,
            'call_title_card_2' => $request->call_title_card_2 ? $request->call_title_card_2 : null,
            'call_number_card_2' => $request->call_number_card_2 ? $request->call_number_card_2 : null,
            'open_url_title_1_card_2' => $request->open_url_title_1_card_2 ? $request->open_url_title_1_card_2 : null,
            'open_url_1_card_2' => $request->open_url_1_card_2 ? $request->open_url_1_card_2 : null,
            'open_url_title_2_card_2' => $request->open_url_title_2_card_2 ? $request->open_url_title_2_card_2 : null,
            'open_url_2_card_2' => $request->open_url_2_card_2 ? $request->open_url_2_card_2 : null,
            'open_url_title_3_card_2' => $request->open_url_title_3_card_2 ? $request->open_url_title_3_card_2 : null,
            'open_url_3_card_2' => $request->open_url_3_card_2 ? $request->open_url_3_card_2 : null,
            'message_length_card_2' => $request->message_card_2 ? strlen($request->message_card_2) : null,
            'message_card_3' => $request->message_card_3,
            'image_card_3' => $imagesCardsArr['imageCard1'] ? $imagesCardsArr['imageCard1'] : null,
            'image_title_card_3' => $request->image_title_card_3 ? $request->image_title_card_3 : null,
            'call_title_card_3' => $request->call_title_card_3 ? $request->call_title_card_3 : null,
            'call_number_card_3' => $request->call_number_card_3 ? $request->call_number_card_3 : null,
            'open_url_title_1_card_3' => $request->open_url_title_1_card_3 ? $request->open_url_title_1_card_3 : null,
            'open_url_1_card_3' => $request->open_url_1_card_3 ? $request->open_url_1_card_3 : null,
            'open_url_title_2_card_3' => $request->open_url_title_2_card_3 ? $request->open_url_title_2_card_3 : null,
            'open_url_2_card_3' => $request->open_url_2_card_3 ? $request->open_url_2_card_3 : null,
            'open_url_title_3_card_3' => $request->open_url_title_3_card_3 ? $request->open_url_title_3_card_3 : null,
            'open_url_3_card_3' => $request->open_url_3_card_3 ? $request->open_url_3_card_3 : null,
            'message_length_card_3' => $request->message_card_3 ? strlen($request->message_card_3) : null,
            'message_card_4' => $request->message_card_4,
            'image_card_4' => $imagesCardsArr['imageCard1'] ? $imagesCardsArr['imageCard1'] : null,
            'image_title_card_4' => $request->image_title_card_4 ? $request->image_title_card_4 : null,
            'call_title_card_4' => $request->call_title_card_4 ? $request->call_title_card_4 : null,
            'call_number_card_4' => $request->call_number_card_4 ? $request->call_number_card_4 : null,
            'open_url_title_1_card_4' => $request->open_url_title_1_card_4 ? $request->open_url_title_1_card_4 : null,
            'open_url_1_card_4' => $request->open_url_1_card_4 ? $request->open_url_1_card_4 : null,
            'open_url_title_2_card_4' => $request->open_url_title_2_card_4 ? $request->open_url_title_2_card_4 : null,
            'open_url_2_card_4' => $request->open_url_2_card_4 ? $request->open_url_2_card_4 : null,
            'open_url_title_3_card_4' => $request->open_url_title_3_card_4 ? $request->open_url_title_3_card_4 : null,
            'open_url_3_card_4' => $request->open_url_3_card_4 ? $request->open_url_3_card_4 : null,
            'message_length_card_4' => $request->message_card_4 ? strlen($request->message_card_4) : null,
            'status' => 1
        );
    }
}
