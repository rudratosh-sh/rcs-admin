<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\SmsTransactionGroup;
use App\SmsTransactionSingle;
use App\RcsBalance;

class SendBulkBasicSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $content;
    protected $singleSmsSingle;
    protected $userId;
    public $timeout = 7200; // 2 hours
    protected $messageId;
    protected $smsTransactionId;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($content, $singleSmsSingle, $userId)
    {
        $this->content = $content;
        $this->singleSmsSingle = $singleSmsSingle;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //checking if data exist with pending sms
        if (!empty($this->singleSmsSingle)) {
            foreach ($this->singleSmsSingle as $single) {
                $this->messageId = $this->generateRandomString(15);
                $response = callRcsSendTextMessage($single->mobile_no, $this->userId, $this->content,$this->messageId);
                //update group id in sms group status
                SmsTransactionSingle::where('id', $single->id)
                    ->update([
                        'status' => 1,
                        'message_id' => $this->messageId,
                        'response' =>$response,
                        'status_code' => $response['status_code']
                    ]);
                $this->smsTransactionId = $single->sms_transaction_group_id;    
            }
            //mark current queue to proccessed
            SmsTransactionGroup::where('id', $this->smsTransactionId)
                ->update([
                    'status' => 2
                ]);
            
            //update stats
            $this->updateStats($this->smsTransactionId);
        }
    }

    // genenrate a unqiue random maessage id 
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function updateStats($smsTransactionId=null){
        if($smsTransactionId==null)
            return false;

        $failedRcsCount = SmsTransactionSingle::where('sms_transaction_group_id', $this->smsTransactionId)->where('status_code',404)->count();
        $succedRcsCount = SmsTransactionSingle::where('sms_transaction_group_id', $this->smsTransactionId)->where('status_code',200)->count();
        $rcsBalanceId = RcsBalance::where('user_id',$this->userId)->orderBy('id','desc')->first(); 
        RcsBalance::where('id',$rcsBalanceId->id)->update(
            [
                'credit_remaining' => $rcsBalanceId->credit_remaining+$failedRcsCount,
                'credit_spend' => $rcsBalanceId->credit_spend-$failedRcsCount,
                'credit_reverted' => $rcsBalanceId->credit_reverted+$failedRcsCount,
            ]
        );
    
        return SmsTransactionGroup::where('id',$this->smsTransactionId)->update([
            'sms_failed' =>$failedRcsCount,
            'sms_success' =>$succedRcsCount
        ]);

    }
}
