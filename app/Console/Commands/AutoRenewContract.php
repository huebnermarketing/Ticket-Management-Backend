<?php

namespace App\Console\Commands;

use App\Models\Contract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoRenewContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-renew-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Renew Contract';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            $this->info('~~~~~~Auto Renew Start Execution ~~~~~~~~');
            Log::info('~~~~~~Auto Renew Start Execution ~~~~~~~~');

            $contracts = Contract::wehre(['is_active'=>1,'is_archive'=>0])->get();
//            foreach($contract )

            Log::info('~~~~~~Auto Renew End Execution ~~~~~~~~');
            $this->info('~~~~~~Auto Renew End Execution ~~~~~~~~');
        }catch (\Exception $e){
            $this->info($e->getMessage());
        }
    }
}
