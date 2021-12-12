<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth, DB;

class SmsTransactionSingle extends Model
{
  protected $table = 'sms_transaction_single';
  use HasFactory;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  static function getCombinedDataBasic($user_id = null)
  {
     
    if ((Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->hasPermissionTo('manage_smart_report')) && $user_id==null)
      return  DB::table('sms_transaction_group')
        ->select(
          'sms_transaction_group.user_id',
          'sms_transaction_group.id',
          'sms_transaction_single.mobile_no',
          DB::raw('(CASE WHEN sms_transaction_single.status_code =200 THEN 1 ELSE 0 END) as credit'),
          DB::raw('(CASE WHEN sms_transaction_single.status_code =200 THEN "Sent" ELSE "Failed" END) as status'),
          // 'sms_transaction_group.message',
          'sms_transaction_single.status_code',
          'sms_transaction_single.created_at',
          'sms_transaction_single.updated_at',
          'sms_transaction_single.delivery_time',
          'sms_transaction_single.read_time',
          DB::raw("GROUP_CONCAT(sms_transaction_group.message) as messages"),
          DB::raw("GROUP_CONCAT(sms_transaction_group.image) as images")
        )
        ->leftJoin('sms_transaction_single', function ($join) {
          $join->on('sms_transaction_group.id', '=', 'sms_transaction_single.sms_transaction_group_id');
        })
        ->groupBy('sms_transaction_group.id')
        ->get()->toArray();
    else{
      if ($user_id == null)
        $user_id = Auth::user()->id;
      return  DB::table('sms_transaction_group')
        ->select(
          'sms_transaction_group.user_id',
          'sms_transaction_group.id',
          'sms_transaction_single.mobile_no',
          DB::raw('(CASE WHEN sms_transaction_single.status_code =200 THEN 1 ELSE 0 END) as credit'),
          DB::raw('(CASE WHEN sms_transaction_single.status_code =200 THEN "Sent" ELSE "Failed" END) as status'),
          // 'sms_transaction_group.message',
          'sms_transaction_single.status_code',
          'sms_transaction_single.created_at',
          'sms_transaction_single.updated_at',
          'sms_transaction_single.delivery_time',
          'sms_transaction_single.read_time',
          DB::raw("GROUP_CONCAT(sms_transaction_group.message) as messages"),
          DB::raw("GROUP_CONCAT(sms_transaction_group.image) as images")
        )
        ->leftJoin('sms_transaction_single', function ($join) {
          $join->on('sms_transaction_group.id', '=', 'sms_transaction_single.sms_transaction_group_id');
        })
        ->where('sms_transaction_group.user_id', $user_id)
        ->groupBy('sms_transaction_group.id')
        ->get()->toArray();
      } 
  }
}
