<?php

namespace App\Http\Controllers;

use App\SmsTransactionGroup;
use App\SmsTransactionGroupAdvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\SmsTransactionSingle;
use App\SmsTransactionSingleAdvance;
use DataTables, Auth, Str;

class ReportsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function smartReport()
    {
        return view('reports.smart-report');
    }
    public function smartReportData()
    {
        $basic = SmsTransactionSingle::getCombinedDataBasic();
        $advance = SmsTransactionSingleAdvance::getCombinedDataAdvance();
        $basic = json_decode(json_encode($basic), true);
        $advance = json_decode(json_encode($advance), true);

        //adding type in array
        $basic = array_map(function ($arr) {
            return $arr + ['smart_type' => 'Basic'];
        }, $basic);

        $advance = array_map(function ($arr) {
            return $arr + ['smart_type' => 'Advance'];
        }, $advance);

        $data = array_merge($basic, $advance);

        return Datatables::of($data)
            // ->rawColumns(['roles','permissions','action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function campaignReport()
    {
        return view('reports.campaign-report');
    }

    public function campaignReportData()
    {
        $basic = SmsTransactionGroup::getCombinedDataBasic();
        $advance = SmsTransactionGroupAdvance::getCombinedDataAdvance();
        $basic = json_decode(json_encode($basic), true);
        $advance = json_decode(json_encode($advance), true);

        //adding type in array
        $basic = array_map(function ($arr) {
            return $arr + ['smart_type' => 'Basic'];
        }, $basic);

        $advance = array_map(function ($arr) {
            return $arr + ['smart_type' => 'Advance'];
        }, $advance);

        $data = array_merge($basic, $advance);

        return Datatables::of($data)
            ->addColumn('download', function ($data) {
                return '
                    <a class="btn btn-success" href="' . url('download-campaign-report?user_id=' . $data['user_id']) . '" >Download</a>';
            })
            ->addIndexColumn()
            ->rawColumns(['download'])
            // ->rawColumns(['roles','permissions','action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function downloadCampaignReport(Request $request)
    {
        if (!$request->user_id)
            return false;
        try {

            /**Prepare data for report */
            $basic = SmsTransactionSingle::getCombinedDataBasic($request->user_id);
            $advance = SmsTransactionSingleAdvance::getCombinedDataAdvance($request->user_id);
            $basic = json_decode(json_encode($basic), true);
            $advance = json_decode(json_encode($advance), true);

            //adding type in array
            $basic = array_map(function ($arr) {
                return $arr + ['smart_type' => 'Basic'];
            }, $basic);

            $advance = array_map(function ($arr) {
                return $arr + ['smart_type' => 'Advance'];
            }, $advance);

            $data = array_merge($basic, $advance);
            /** end data preparation */
            $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, 
                 pre-check=0',
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename=Campgain_Report' . $request->user_id . '.csv',
                'Expires'             => '0',
                'Pragma'              => 'public'
            ];

            $list = $data;

            # add headers for each column in the CSV download
            array_unshift($list, array_keys($list[0]));

            $callback = function () use ($list) {
                $FH = fopen('php://output', 'w');
                foreach ($list as $row) {
                    fputcsv($FH, $row);
                }
                fclose($FH);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return ($e->getMessage());
        }
    }
}
