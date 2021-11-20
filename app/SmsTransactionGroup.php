<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    return $query->where('status',0)->get();
  }
}
