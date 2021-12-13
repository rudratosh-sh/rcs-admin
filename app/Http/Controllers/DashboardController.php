<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RcsBalance;
use App\SmsTransactionGroup;
use App\SmsTransactionGroupAdvance;
use Auth, DateTime, DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view(
            'dashboard',
            [
                array_merge($this->getBalance(Auth::user()->id),
                $this->getStatsAdvance(),
                $this->getStatsBasic(),
                $this->adminStatsBasic(),
                $this->adminStatsAdvance())
            ][0]
        );
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

    private function getStatsBasic()
    {
        $basicCountToday = SmsTransactionGroup::select(
            DB::raw('SUM(sms_count) as basic_sms_count_today'),
            DB::raw('SUM(sms_failed) as basic_sms_failed_today'),
            DB::raw('SUM(sms_success) as basic_sms_success_today')
        )
            ->whereDate('created_at', date('Y-m-d'))
            ->where('status', 2)
            ->where('user_id', Auth::user()->id)->first();

        $basicCountYesterday = SmsTransactionGroup::select(
            DB::raw('SUM(sms_count) as basic_sms_count_yesterday'),
            DB::raw('SUM(sms_failed) as basic_sms_failed_yesterday'),
            DB::raw('SUM(sms_success) as basic_sms_success_yesterday')
        )
            ->whereDate('created_at', date('Y-m-d', strtotime("-1 days")))
            ->where('status', 2)
            ->where('user_id', Auth::user()->id)->first();

        $basicCountWeek = SmsTransactionGroup::select(
            DB::raw('SUM(sms_count) as basic_sms_count_week'),
            DB::raw('SUM(sms_failed) as basic_sms_failed_week'),
            DB::raw('SUM(sms_success) as basic_sms_success_week')
        )
            ->whereDate('created_at', '>=', date('Y-m-d', strtotime("-7 days")))
            ->where('status', 2)
            ->where('user_id', Auth::user()->id)->first();

        $basicCountOverall = SmsTransactionGroup::select(
            DB::raw('SUM(sms_count) as basic_sms_count_all'),
            DB::raw('SUM(sms_failed) as basic_sms_failed_all'),
            DB::raw('SUM(sms_success) as basic_sms_success_all')
        )
            ->where('status', 2)
            ->where('user_id', Auth::user()->id)->first();

        return [
            'basicCountToday'=>$basicCountToday,
            'basicCountYesterday'=>$basicCountYesterday,
            'basicCountWeek'=>$basicCountWeek,
            'basicCountOverall'=>$basicCountOverall
        ];
    }

    private function getStatsAdvance()
    {
        $advanceCountToday = SmsTransactionGroupAdvance::select(
            DB::raw('SUM(sms_count) as advance_sms_count_today'),
            DB::raw('SUM(sms_failed) as advance_sms_failed_today'),
            DB::raw('SUM(sms_success) as advance_sms_success_today')
        )
            ->whereDate('created_at', date('Y-m-d'))
            ->where('status', 2)
            ->where('user_id', Auth::user()->id)->first();

        $advanceCountYesterday = SmsTransactionGroupAdvance::select(
            DB::raw('SUM(sms_count) as advance_sms_count_yesterday'),
            DB::raw('SUM(sms_failed) as advance_sms_failed_yesterday'),
            DB::raw('SUM(sms_success) as advance_sms_success_yesterday')
        )
            ->whereDate('created_at', date('Y-m-d', strtotime("-1 days")))
            ->where('status', 2)
            ->where('user_id', Auth::user()->id)->first();

        $advanceCountWeek = SmsTransactionGroupAdvance::select(
            DB::raw('SUM(sms_count) as advance_sms_count_week'),
            DB::raw('SUM(sms_failed) as advance_sms_failed_week'),
            DB::raw('SUM(sms_success) as advance_sms_success_week')
        )
            ->whereDate('created_at', '>=', date('Y-m-d', strtotime("-7 days")))
            ->where('status', 2)
            ->where('user_id', Auth::user()->id)->first();

        $advanceCountOverall = SmsTransactionGroupAdvance::select(
            DB::raw('SUM(sms_count) as advance_sms_count_all'),
            DB::raw('SUM(sms_failed) as advance_sms_failed_all'),
            DB::raw('SUM(sms_success) as advance_sms_success_all')
        )
            ->where('status', 2)
            ->where('user_id', Auth::user()->id)->first();

        return [
            'advanceCountToday'=>$advanceCountToday,
            'advanceCountYesterday'=>$advanceCountYesterday,
            'advanceCountWeek'=>$advanceCountWeek,
            'advanceCountOverall'=>$advanceCountOverall
        ];
    }

    private function adminStatsBasic()
    {
        $basicCountToday = SmsTransactionGroup::select(
            DB::raw('SUM(sms_count) as basic_sms_count_today'),
            DB::raw('SUM(sms_failed) as basic_sms_failed_today'),
            DB::raw('SUM(sms_success) as basic_sms_success_today')
        )
            ->whereDate('created_at', date('Y-m-d'))
            ->where('status', 2)->first();
            // ->where('user_id', Auth::user()->id)->first();

        $basicCountYesterday = SmsTransactionGroup::select(
            DB::raw('SUM(sms_count) as basic_sms_count_yesterday'),
            DB::raw('SUM(sms_failed) as basic_sms_failed_yesterday'),
            DB::raw('SUM(sms_success) as basic_sms_success_yesterday')
        )
            ->whereDate('created_at', date('Y-m-d', strtotime("-1 days")))
            ->where('status', 2)->first();
            // ->where('user_id', Auth::user()->id)->first();

        $basicCountWeek = SmsTransactionGroup::select(
            DB::raw('SUM(sms_count) as basic_sms_count_week'),
            DB::raw('SUM(sms_failed) as basic_sms_failed_week'),
            DB::raw('SUM(sms_success) as basic_sms_success_week')
        )
            ->whereDate('created_at', '>=', date('Y-m-d', strtotime("-7 days")))
            ->where('status', 2)->first();
            // ->where('user_id', Auth::user()->id)->first();

        $basicCountOverall = SmsTransactionGroup::select(
            DB::raw('SUM(sms_count) as basic_sms_count_all'),
            DB::raw('SUM(sms_failed) as basic_sms_failed_all'),
            DB::raw('SUM(sms_success) as basic_sms_success_all')
        )
            ->where('status', 2)->first();
            // ->where('user_id', Auth::user()->id)->first();

        return [
            'basicCountTodayAdmin'=>$basicCountToday,
            'basicCountYesterdayAdmin'=>$basicCountYesterday,
            'basicCountWeekAdmin'=>$basicCountWeek,
            'basicCountOverallAdmin'=>$basicCountOverall
        ];
    }

    private function adminStatsAdvance()
    {
        $advanceCountToday = SmsTransactionGroupAdvance::select(
            DB::raw('SUM(sms_count) as advance_sms_count_today'),
            DB::raw('SUM(sms_failed) as advance_sms_failed_today'),
            DB::raw('SUM(sms_success) as advance_sms_success_today')
        )
            ->whereDate('created_at', date('Y-m-d'))
            ->where('status', 2)->first();
            // ->where('user_id', Auth::user()->id)->first();

        $advanceCountYesterday = SmsTransactionGroupAdvance::select(
            DB::raw('SUM(sms_count) as advance_sms_count_yesterday'),
            DB::raw('SUM(sms_failed) as advance_sms_failed_yesterday'),
            DB::raw('SUM(sms_success) as advance_sms_success_yesterday')
        )
            ->whereDate('created_at', date('Y-m-d', strtotime("-1 days")))
            ->where('status', 2)->first();
            // ->where('user_id', Auth::user()->id)->first();

        $advanceCountWeek = SmsTransactionGroupAdvance::select(
            DB::raw('SUM(sms_count) as advance_sms_count_week'),
            DB::raw('SUM(sms_failed) as advance_sms_failed_week'),
            DB::raw('SUM(sms_success) as advance_sms_success_week')
        )
            ->whereDate('created_at', '>=', date('Y-m-d', strtotime("-7 days")))
            ->where('status', 2)->first();
            // ->where('user_id', Auth::user()->id)->first();

        $advanceCountOverall = SmsTransactionGroupAdvance::select(
            DB::raw('SUM(sms_count) as advance_sms_count_all'),
            DB::raw('SUM(sms_failed) as advance_sms_failed_all'),
            DB::raw('SUM(sms_success) as advance_sms_success_all')
        )
            ->where('status', 2)->first();
            // ->where('user_id', Auth::user()->id)->first();

        return [
            'advanceCountTodayAdmin'=>$advanceCountToday,
            'advanceCountYesterdayAdmin'=>$advanceCountYesterday,
            'advanceCountWeekAdmin'=>$advanceCountWeek,
            'advanceCountOverallAdmin'=>$advanceCountOverall
        ];
    }
}
