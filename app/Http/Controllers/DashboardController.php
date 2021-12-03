<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RcsBalance;
use Auth,DateTime;
class DashboardController extends Controller
{
    public function index()
    {

        return view('dashboard', $this->getBalance(Auth::user()->id));
    }

    function getBalance($userId = null)
    {
        if (!$userId)
            return [];

        $balance = RcsBalance::where('user_id', $userId)->get();
        $balance = $balance->sortByDesc('id');
        $totalMessageCredit = $creditSpend = $creditReverted = $creditExpired = $creditRemaining = $lastRecharged = 0;
        $lastRechargedOn = $creditExpiredOn = null;

        if (!empty($balance)) {
            foreach ($balance as $key => $bal) {
                if ($key == 0) {
                    $lastRechargedOn = date_format(date_create($bal->created_at), "d F Y");
                    $lastRecharged = $bal;
                }
                //valid till date
                $validTillDate = new DateTime($bal->valid_till);
                $today = new DateTime("today");
                if ($validTillDate >= $today) {
                    $creditRemaining = $bal->credit_remaining + $creditRemaining;
                } else {
                    $creditExpired = $bal->credit_remaining + $creditExpired;
                }
                //find out expiry date of credit
                if ($creditExpiredOn == null) {
                    $creditExpiredOn = $validTillDate;
                } elseif ($creditExpiredOn <= $validTillDate) {
                    $creditExpiredOn = $validTillDate;
                }

                $totalMessageCredit = $bal->recharge + $totalMessageCredit;
                $creditSpend = $bal->credit_spend + $creditSpend;
                $creditReverted = $bal->credit_reverted + $creditReverted;
            }
            return  array(
                'totalMessageCredit' => $totalMessageCredit,
                'creditSpend' => $creditSpend,
                'creditReverted' => $creditReverted,
                'creditExpired' => $creditExpired,
                'creditRemaining' => $creditRemaining,
                'lastRechargedOn' => $lastRechargedOn,
                'creditExpiredOn' => $creditExpiredOn != null ? date_format($creditExpiredOn, "d F Y") : 'Not Available',
                'lastRecharged' => $lastRecharged
            );
        }
    }
}
