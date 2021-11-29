<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

use App\FilterMessages;

class FilterMobileNo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $content;
    protected $userId;
    public $timeout = 7200; // 2 hours
    protected $contentId;
    protected $reachableGlobal;
    protected $notRechableGlobal;
    protected $mergeGlobal;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($content, $userId, $contentId)
    {
        $this->content = $content;
        $this->userId = $userId;
        $this->contentId = $contentId;
        $this->reachableGlobal = [];
        $this->notRechableGlobal = [];
        $this->mergeGlobal= [];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //checking if data exist with pending sms
        if (!empty($this->content) && $this->contentId) {
            try {
                $chunkRes = array_chunk($this->content, 9000);
                foreach ($chunkRes as $chunk) {
                    $response = callRcsValidate($chunk, $this->userId, $this->contentId);
                    $rechableUsers = $response['response'] != '' ? $response['response'] : null;
                    if ($rechableUsers != null) {
                        $result = $this->filter($rechableUsers->reachableUsers, $chunk);
                    }
                }
                $this->createRelation();
                $this->storeCsvFile();
                exit;
            } catch (\Exception $e) {
                echo ($e);
            }
        }
        exit;
    }

    private function filter($rechable = null, $response = null)
    {
        $notRechable = array_diff($response, $rechable);
        $this->notRechableGlobal = array_merge($this->notRechableGlobal, $notRechable);
        $this->reachableGlobal = array_merge($this->reachableGlobal, $rechable);
    }

    private function createRelation()
    {
        $this->reachableGlobal = array_combine($this->reachableGlobal, array_map(
            function ($v) {
                return 'RCS Reachable';
            },
            $this->reachableGlobal
        ));

        $this->notRechableGlobal = array_combine($this->notRechableGlobal, array_map(
            function ($v) {
                return 'RCS Not Reachable';
            },
            $this->notRechableGlobal
        ));

        $this->mergeGlobal = array_merge($this->reachableGlobal,$this->notRechableGlobal);
    }
    private function storeCsvFile()
    {
        
        if (!$this->mergeGlobal || !$this->contentId)
            return false;
        try {
            $temp = [];
            foreach($this->mergeGlobal as $key =>$value){
                $temp[] = array(
                    $key => $value
                );
            }
            $tempName = 'FINAL' . time() . '_' . Str::uuid()->toString();
            $path = 'uploads/csv/';
            $fileName =  $tempName  . '.csv';
            $file = fopen(public_path().'/'.$path . $fileName, 'w');
            foreach ($temp as $key =>$row) {
                $keys = array_keys($row);
                fputcsv($file, array($keys[0], $row[$keys[0]]));
            }
            fclose($file);
            FilterMessages::where('id', $this->contentId)->update(
                ['status' => 1, 'downloaded_file' => 'csv/' . $fileName]
            );
        } catch (\Exception $e) {
            return $e;
        }
    }
}
