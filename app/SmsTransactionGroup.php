<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class SmsTransactionGroup extends Model
{
  protected $table = 'sms_transaction_group';
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
  static function getCombinedDataBasic()
  {
    if (Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->hasPermissionTo('manage_campaign_report'))
      return  DB::table('sms_transaction_group')
        ->select(
          'sms_transaction_group.user_id',
          'sms_transaction_group.id',
          'sms_transaction_group.created_at',
          'sms_transaction_group.updated_at',
          'sms_transaction_group.status',
          'sms_transaction_group.message_form',
          'sms_transaction_group.sms_count',
          'sms_transaction_group.sms_failed',
          'sms_transaction_group.sms_success',
          DB::raw("GROUP_CONCAT(sms_transaction_group.message) as messages"),
          DB::raw("GROUP_CONCAT(sms_transaction_group.image) as images"),
          'users.email',
          'users.name',
          'users.mobile_no'
        )
        ->leftJoin('users', function ($join) {
          $join->on('sms_transaction_group.user_id', '=', 'users.id');
        })
        ->groupBy('sms_transaction_group.id')
        ->orderBy('sms_transaction_group.id','DESC')
        ->get()->toArray();
    else
      return  DB::table('sms_transaction_group')
        ->select(
          'sms_transaction_group.user_id',
          'sms_transaction_group.id',
          'sms_transaction_group.created_at',
          'sms_transaction_group.updated_at',
          'sms_transaction_group.status',
          'sms_transaction_group.sms_count',
          'sms_transaction_group.sms_failed',
          'sms_transaction_group.sms_success',
          'sms_transaction_group.message_form',
          DB::raw("GROUP_CONCAT(sms_transaction_group.message) as messages"),
          DB::raw("GROUP_CONCAT(sms_transaction_group.image) as images"),
          'users.email',
          'users.name',
          'users.mobile_no'
        )
        ->leftJoin('users', function ($join) {
          $join->on('sms_transaction_group.user_id', '=', 'users.id');
        })
        ->where('sms_transaction_group.user_id', Auth::user()->id)
        ->orderBy('sms_transaction_group.id','DESC')
        ->groupBy('sms_transaction_group.id')
        ->get()->toArray();
  }
}
