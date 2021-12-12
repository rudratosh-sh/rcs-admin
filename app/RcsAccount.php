<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth, DB;
class RcsAccount extends Model
{   
    use HasFactory;
    protected $table = 'rcs_accounts';
    protected $guarded = [];

    static function getAccounts()
  {
   //if super admin
    if(Auth::user()->id==1 || Auth::user()->id==2 || Auth::user()->hasPermissionTo('rcs_balance_management'))
        return  DB::table('rcs_accounts')
        ->select(
            'rcs_accounts.user_id',
            'rcs_accounts.type',
            'rcs_accounts.balance',
            'rcs_accounts.validity',
            'rcs_accounts.created_by',
            'rcs_accounts.created_at',
            'rcs_accounts.updated_at',
            'rcs_balance.credit_remaining',
            'users.name',
            'users.mobile_no'
        )
        ->leftJoin('rcs_balance', function ($join) {
            $join->on('rcs_accounts.user_id', '=', 'rcs_balance.user_id');
        })
        ->leftJoin('users', function ($join) {
            $join->on('rcs_accounts.user_id', '=', 'users.id');
        })
        // ->where('rcs_accounts.user_id', 33)
        ->groupBy('rcs_accounts.id')
        ->get()
        ->toArray();
    // elseif(Auth::user()->hasPermissionTo('rcs_balance_management'))
    //     return  DB::table('rcs_accounts')
    //         ->select(
    //             'rcs_accounts.user_id',
    //             'rcs_accounts.type',
    //             'rcs_accounts.balance',
    //             'rcs_accounts.validity',
    //             'rcs_accounts.created_by',
    //             'rcs_accounts.created_at',
    //             'rcs_accounts.updated_at',
    //             'rcs_balance.credit_remaining',
    //             'users.name',
    //             'users.mobile_no'
    //         )
    //         ->leftJoin('rcs_balance', function ($join) {
    //             $join->on('rcs_accounts.user_id', '=', 'rcs_balance.user_id');
    //         })
    //         ->leftJoin('users', function ($join) {
    //             $join->on('rcs_accounts.user_id', '=', 'users.id');
    //         })
    //         ->where('rcs_accounts.created_by', Auth::user()->id)
    //         ->groupBy('rcs_accounts.id')
    //         ->get()
    //         ->toArray(); 
    elseif(Auth::user()->hasPermissionTo('rcs_account_report'))
        return  DB::table('rcs_accounts')
            ->select(
                'rcs_accounts.user_id',
                'rcs_accounts.type',
                'rcs_accounts.balance',
                'rcs_accounts.validity',
                'rcs_accounts.created_by',
                'rcs_accounts.created_at',
                'rcs_accounts.updated_at',
                'rcs_balance.credit_remaining',
                'users.name',
                'users.mobile_no'
            )
            ->leftJoin('rcs_balance', function ($join) {
                $join->on('rcs_accounts.user_id', '=', 'rcs_balance.user_id');
            })
            ->leftJoin('users', function ($join) {
                $join->on('rcs_accounts.user_id', '=', 'users.id');
            })
            ->where('rcs_accounts.user_id', Auth::user()->id)
            ->groupBy('rcs_accounts.id')
            ->get()
            ->toArray();
    else
        return [];                       
  }
}
