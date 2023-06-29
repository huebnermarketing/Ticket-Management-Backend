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

            $contracts = Contract::with('duration','contractServicesTypes','productService')->where(['contract_status_id'=>1,'is_auto_renew'=>1])->get();
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
                    Log::info('called'.$todayDate);
                    $contract->start_date = $startDate;
                    $contract->end_date = $endData;
                    $contract->parent_id = $contract['id'];
                    $contract->contract_status_id = 3;
                    $contract->open_ticket_contract = 1;
                    $storeContract = $this->contractRepository->storeContract($contract);
                    Log::info('New Contract Id: '.$storeContract['id']);
                    $types['contract_type_id'] = [];
                    foreach($contract->contractServicesTypes as $type){
                        array_push($types['contract_type_id'],$type->contract_type_id);
                    }
                    $this->contractRepository->storeContractService($storeContract['id'],$types);

                    $productTypes['contract_product_service_id'] = [];
                    foreach($contract->productService as $types){
                        $productserviceType =  [
                            'product_service_id'=>$types->product_service_id,
                            'product_qty'=>$types->product_qty,
                            'product_amount'=>$types->product_amount,
                        ];
                        array_push($productTypes['contract_product_service_id'],$productserviceType);
                    }
                    $this->contractRepository->storeContractProductService($storeContract['id'],$productTypes);
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
