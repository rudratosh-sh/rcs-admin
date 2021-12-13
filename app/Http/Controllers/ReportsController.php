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

        $x[] = $basic;
        $x[] = $advance;
        $data = call_user_func_array('array_merge', $x);
        $data = ($this->sortArrary($data));
        //$data = array_merge($basic, $advance);

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

        $x[] = $basic;
        $x[] = $advance;
        $data = call_user_func_array('array_merge', $x);
        $data = ($this->sortArrary($data));
        return Datatables::of($data)
            ->addColumn('download', function ($data) {
                return '
                    <a class="btn btn-success text-white" target="_blank" href="' . url('download-campaign-report?user_id=' . $data['user_id'] . '&group_id=' . $data['id'] . '&type=' . $data['message_form']) . '" >Download</a>';
            })
            ->addColumn('status', function ($data) {
                if ($data['status'] == 0)
                    return '<a class="btn btn-warning text-white">Pending</a>';
                else
                    return '<a class="btn btn-success text-white">Success</a>';
            })
            ->addIndexColumn()
            ->rawColumns(['download','status'])
            // ->rawColumns(['roles','permissions','action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function downloadCampaignReport(Request $request)
    {
        if (!$request->user_id || !$request->group_id || !$request->type)
            return false;
        try {

            /**Prepare data for report */
            if ($request->type == 'BASIC') {
                $basic = SmsTransactionSingle::getCombinedDataBasicDownload($request->user_id, $request->group_id);
                $basic = json_decode(json_encode($basic), true);
                //adding type in array
                $data = array_map(function ($arr) {
                    return $arr + ['smart_type' => 'Basic'];
                }, $basic);
            }
            if ($request->type == 'ADVANCE') {
                $advance = SmsTransactionSingleAdvance::getCombinedDataAdvanceDownload($request->user_id, $request->group_id);
                $advance = json_decode(json_encode($advance), true);
                $data = array_map(function ($arr) {
                    return $arr + ['smart_type' => 'Advance'];
                }, $advance);
            }

            // $data = array_merge($basic, $advance);
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

    private function sortArrary($arr = [])
    {
        function date_compare($a, $b)
        {
            $t1 = strtotime($a['updated_at']);
            $t2 = strtotime($b['updated_at']);
            return $t1 - $t2;
        }
        usort($arr, function ($a, $b) {
            $t1 = strtotime($a['updated_at']);
            $t2 = strtotime($b['updated_at']);
            return $t1 - $t2;
        });
        return (array_reverse($arr));
    }
}
