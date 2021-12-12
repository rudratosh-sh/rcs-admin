<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class SmsTransactionGroupAdvance extends Model
{
  protected $table = 'sms_transaction_group_advance';
  use HasFactory;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  // get pending sms queue
  public function scopeStatus($query)
  {
    return $query->where('status', 0)->get();
  }

  static function getCombinedDataAdvance()
  {
    if (Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->hasPermissionTo('manage_campaign_report'))
      return  DB::table('sms_transaction_group_advance')
        ->select(
          'sms_transaction_group_advance.user_id',
          'sms_transaction_group_advance.id',
          'sms_transaction_group_advance.created_at',
          'sms_transaction_group_advance.updated_at',
          'sms_transaction_group_advance.status',
          'sms_transaction_group_advance.sms_count',
          'sms_transaction_group_advance.sms_failed',
          'sms_transaction_group_advance.sms_success',
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
          as images"),
          'users.email',
          'users.name',
          'users.mobile_no'
        )
        ->leftJoin('users', function ($join) {
          $join->on('sms_transaction_group_advance.user_id', '=', 'users.id');
        })
        ->groupBy('sms_transaction_group_advance.id')
        // ->orderBy('sms_transaction_group_advance.id','DESC')
        ->get()->toArray();
    else
      return  DB::table('sms_transaction_group_advance')
        ->select(
          'sms_transaction_group_advance.user_id',
          'sms_transaction_group_advance.id',
          'sms_transaction_group_advance.created_at',
          'sms_transaction_group_advance.updated_at',
          'sms_transaction_group_advance.status',
          'sms_transaction_group_advance.sms_count',
          'sms_transaction_group_advance.sms_failed',
          'sms_transaction_group_advance.sms_success',
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
      as images"),
      'users.email',
      'users.name',
      'users.mobile_no'
    )
    ->leftJoin('users', function ($join) {
      $join->on('sms_transaction_group_advance.user_id', '=', 'users.id');
    })
        ->where('sms_transaction_group_advance.user_id', Auth::user()->id)
        ->groupBy('sms_transaction_group_advance.id')
        ->orderBy('sms_transaction_group_advance.id','DESC')
        ->get()->toArray();
  }
}
