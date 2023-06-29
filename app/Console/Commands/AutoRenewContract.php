<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\ContractProductService;
use App\Models\ContractServiceType;
use App\Repositories\Contract\ContractRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Traits\CommonTrait;

class AutoRenewContract extends Command
{
    use CommonTrait;
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
    private $contractRepository;

    public function handle(ContractRepositoryInterface $contractRepository)
    {
        try{
            $this->contractRepository = $contractRepository;
            $this->info('~~~~~~Auto Renew Start Execution ~~~~~~~~');
            Log::info('~~~~~~Auto Renew Start Execution ~~~~~~~~');

            $contracts = Contract::with('duration','contractServicesTypes','productService')->where(['is_active'=>1,'is_auto_renew'=>1,'id'=>2])->get();
            foreach($contracts as $contract){
                $todayDate = date('Y-m-d');
                $startDate = date('Y-m-d', strtotime($contract->end_date.' + 1 days'));

                if($contract->duration->slug == 'year' || $contract->duration->slug == 'half-year' || $contract->duration->slug == 'qtr'){
                    $getBeforeDate = date('Y-m-d', strtotime($contract->end_date.' - 30 days'));
                }elseif($contract->duration->slug == 'month'){
                    $getBeforeDate = date('Y-m-d', strtotime($contract->end_date.' - 10 days'));
                }

                if($contract->duration->slug == 'year'){
                    $endData = date('Y-m-d', strtotime($contract->end_date.' + 365 days'));
                }elseif($contract->duration->slug == 'half-year'){
                    $endData = date('Y-m-d', strtotime($contract->end_date.' + 180 days'));
                }elseif($contract->duration->slug == 'qtr'){
                    $endData = date('Y-m-d', strtotime($contract->end_date.' + 120 days'));
                }elseif($contract->duration->slug == 'month'){
                    $endData = date('Y-m-d', strtotime($contract->end_date.' + 30 days'));
                }
                if($todayDate == $getBeforeDate){
                    $contract->start_date = $startDate;
                    $contract->end_date = $endData;
                    $contract->parent_id = array($contract->id);
                    $contract->contract_status_id = 3;
                    $contract->is_active = 0;
                    $storeContract = $this->contractRepository->storeContract(array($contract));
                    $this->contractRepository->storeContractService($storeContract['id'],array($contract));
                    $this->contractRepository->storeContractProductService($storeContract['id'],array($contract));
                }
            }
            Log::info('~~~~~~Auto Renew End Execution ~~~~~~~~');
            $this->info('~~~~~~Auto Renew End Execution ~~~~~~~~');
        }catch (\Exception $e){
            $this->info($e->getLine());
            $this->info($e->getMessage());
        }
    }
}
