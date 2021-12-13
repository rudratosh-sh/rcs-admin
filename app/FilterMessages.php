<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB,Auth;

class FilterMessages extends Model
{
  protected $table = 'filter_messages';
  use HasFactory;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  static function getFilters(){
    if(Auth::user()->hasPermissionTo('manage_filter_message'))
        return  DB::table('filter_messages')
        ->select(
          'filter_messages.*',
          'users.email',
          'users.name as username',
          'users.mobile_no'
        )
        ->leftJoin('users', function ($join) {
          $join->on('filter_messages.user_id', '=', 'users.id');
        })
        ->groupBy('filter_messages.id')
        ->orderBy('filter_messages.id','DESC')
        ->get();
    else
        return  DB::table('filter_messages')
        ->select(
          'filter_messages.*',
          'users.email',
          'users.name as username',
          'users.mobile_no'
        )
        ->leftJoin('users', function ($join) {
          $join->on('filter_messages.user_id', '=', 'users.id');
        })
        ->where('filter_messages.user_id',Auth::user()->id)
        ->groupBy('filter_messages.id')
        ->orderBy('filter_messages.id','DESC')
        ->get();

  }
}
