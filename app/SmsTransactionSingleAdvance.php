<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class SmsTransactionSingleAdvance extends Model
{
  protected $table = 'sms_transaction_single_advance';
  use HasFactory;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  static function getCombinedDataAdvance($user_id =null)
  {
    
    if ((Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->hasPermissionTo('manage_smart_report')) && $user_id==null)
      return  DB::table('sms_transaction_group_advance')
        ->select(
          'sms_transaction_group_advance.user_id',
          'sms_transaction_group_advance.id',
          'sms_transaction_single_advance.mobile_no',
          DB::raw('(CASE WHEN sms_transaction_single_advance.status_code =200 THEN 1 ELSE 0 END) as credit'),
          DB::raw('(CASE WHEN sms_transaction_single_advance.status_code =200 THEN "Sent" ELSE "Failed" END) as status'),
          // 'sms_transaction_group_advance.message',
          'sms_transaction_single_advance.status_code',
          'sms_transaction_single_advance.created_at',
          'sms_transaction_single_advance.updated_at',
          'sms_transaction_single_advance.delivery_time',
          'sms_transaction_single_advance.read_time',
          DB::raw("GROUP_CONCAT(
                      sms_transaction_group_advance.message_card_1,',',
                      sms_transaction_group_advance.message_card_2,',',
                      sms_transaction_group_advance.message_card_3,',',
                      sms_transaction_group_advance.message_card_4) 
                  as messages"),
          DB::raw("GROUP_CONCAT(
                    sms_transaction_group_advance.image_card_1,',',
                    sms_transaction_group_advance.image_card_2,',',
                    sms_transaction_group_advance.image_card_3,',',
                    sms_transaction_group_advance.image_card_4) 
                as images")
        )
        ->leftJoin('sms_transaction_single_advance', function ($join) {
          $join->on('sms_transaction_group_advance.id', '=', 'sms_transaction_single_advance.sms_transaction_group_advance_id');
        })
        ->orderBy('sms_transaction_group_advance.id','DESC')
        ->groupBy('sms_transaction_group_advance.id')
        ->get()->toArray();
    else{
      if ($user_id == null)
        $user_id = Auth::user()->id;
      return  DB::table('sms_transaction_group_advance')
        ->select(
          'sms_transaction_group_advance.user_id',
          'sms_transaction_group_advance.id',
          'sms_transaction_single_advance.mobile_no',
          DB::raw('(CASE WHEN sms_transaction_single_advance.status_code =200 THEN 1 ELSE 0 END) as credit'),
          DB::raw('(CASE WHEN sms_transaction_single_advance.status_code =200 THEN "Sent" ELSE "Failed" END) as status'),
          // 'sms_transaction_group_advance.message',
          'sms_transaction_single_advance.status_code',
          'sms_transaction_single_advance.created_at',
          'sms_transaction_single_advance.updated_at',
          'sms_transaction_single_advance.delivery_time',
          'sms_transaction_single_advance.read_time',
          DB::raw("GROUP_CONCAT(
              sms_transaction_group_advance.message_card_1,',',
              sms_transaction_group_advance.message_card_2,',',
              sms_transaction_group_advance.message_card_3,',',
              sms_transaction_group_advance.message_card_4) 
          as messages"),
          DB::raw("GROUP_CONCAT(
            sms_transaction_group_advance.image_card_1,',',
            sms_transaction_group_advance.image_card_2,',',
            sms_transaction_group_advance.image_card_3,',',
            sms_transaction_group_advance.image_card_4) 
        as images")
        )
        ->leftJoin('sms_transaction_single_advance', function ($join) {
          $join->on('sms_transaction_group_advance.id', '=', 'sms_transaction_single_advance.sms_transaction_group_advance_id');
        })
        ->where('sms_transaction_group_advance.user_id',$user_id)
        ->orderBy('sms_transaction_group_advance.id','DESC')
        ->groupBy('sms_transaction_group_advance.id')
        ->get()->toArray();
      }    
  }
}
