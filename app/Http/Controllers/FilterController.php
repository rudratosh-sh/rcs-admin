<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use Response;
use Redirect;
use App\FilterMessages;
use DataTables, Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class FilterController extends Controller
{
    protected $totalCount;
    protected $reachableCount;
    protected $notReachableCount;

    public function index(Request $request)
    {
        $data['filters'] = FilterMessages::get();
        return view('filter-messages.index')->with($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_name' => 'required',
            'csv_file' => 'required|mimes:csv,txt|max:10240',
        ], [
            'file_name.required' => 'File Name is Required',
            'csv_file.mimes' => 'Only CSV File Allowed',
            'csv_file.max' => 'Only Image 10 MB File Allowed',
            'csv_file.required' => 'CSV File is Required',
        ]);

        //upload csv file
        if ($request->hasFile('csv_file')) {
            $tempName = 'UPLOADED_' . time() . '_' . $request->csv_file->getClientOriginalName();
            $csvPath = $request->file('csv_file')->storeAs('csv', str_replace(' ', '', $tempName));
        }
        try {
            $store = FilterMessages::create(
                array(
                    'name' => $request->file_name,
                    'user_id' => Auth::user()->id,
                    'tags' => ($request->tags != '') ? $request->tags : '',
                    'uploaded_file' => $csvPath
                )
            );
            // store sms transaction group advance
            if ($store)
                return redirect('filter-messages')->with('success', 'File added added in Queue');
            else
                return redirect('filter-messages')->with('error', 'Failed to create request! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * filter smart messages
     *
     * @return void
     */
    public function filterMobileNumbers()
    {
        $pendingFilter = FilterMessages::where('status', 0)->first();
        if (!empty($pendingFilter) && $pendingFilter->id) {
            $response = $this->validateMobileNo($pendingFilter);
            if ($response['status'] == false) {
                $this->storeErrorFile($response,$pendingFilter->id);
            }else{
                $job = (new \App\Jobs\FilterMobileNo($response['mobileNOS'],$pendingFilter->user_id,$pendingFilter->id))
                ->delay(
                    now()
                        ->addSeconds(2)
                );
            dispatch($job);
            echo "filter send successfully in the background...";
            }
        }
    }

    private function validateMobileNo($pendingFilter = null)
    {
        $file = file_get_contents(Storage::disk('local')->path($pendingFilter->uploaded_file));
        $data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $file));
        $data = array_filter((array_reduce($data, 'array_merge', array())));
        $this->totalCount = count($data); 
        $data = implode(',', $data);
        $mobileNoEarlier = senitizeMobileNumbers($data);
        $mobileFiltered = array_filter($mobileNoEarlier, function ($element) {
            if (strlen($element) != 13)
                return false;
            else
                return true;
        });
        $mobileDiff = array_diff($mobileNoEarlier, $mobileFiltered);
        if (empty($mobileDiff))
            return array('status' => true, 'mobileNOS' => $mobileFiltered);
        else
            return array('status' => false, 'mobileNOS' => $mobileDiff);
    }

    private function storeErrorFile($response = null,$id=null)
    {
        if (!$response || !$id)
            return false;
        try{
            $tempName = 'ERROR' . time() . '_' . Str::uuid()->toString();
            $path = 'uploads/csv/';
            $fileName =  $tempName  . '.csv';
            $file = fopen(public_path().'/'.$path . $fileName, 'w');
            $columns = array('Invalid Mobile No');
            fputcsv($file, $columns);
            fputcsv($file, $response['mobileNOS']);
            fclose($file);

            FilterMessages::where('id',$id)->update(
                [
                    'status'=>2,
                    'error_file'=>'csv/'.$fileName,
                    'total_counts'=> $this->totalCount,
                    'invalid_counts' => count($response['mobileNOS'])
                ]
            );
        }catch(\Exception $e){
            return ($e->getMessage());
        }    
    }
}
