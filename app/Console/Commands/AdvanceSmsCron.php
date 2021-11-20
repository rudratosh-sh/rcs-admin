<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AdvanceSmsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:AdvanceSmsCron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This job will fetch advance sms send data and add to queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
 

}
