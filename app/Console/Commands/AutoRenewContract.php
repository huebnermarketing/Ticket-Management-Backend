<?php

namespace App\Console\Commands;

use App\Mail\SendContractReNewEmailNotification;
use App\Models\Contract;
use App\Models\ContractProductService;
use App\Models\ContractServiceType;
use App\Models\CustomerLocations;
use App\Models\CustomerPhones;
use App\Models\Customers;
use App\Models\User;
use App\Repositories\Contract\ContractRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Mail;

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

            $contracts = Contract::with('duration','contractServicesTypes','productService')->where(['contract_status_id'=>getStatusId(10001)->id,'is_auto_renew'=>1])->get();
            foreach($contracts as $contract){
                $todayDate = date('Y-m-d');
                $startDate = date('Y-m-d', strtotime($contract->end_date.' + 1 days'));

                if($contract->duration->slug == 'year' || $contract->duration->slug == 'half-year' || $contract->duration->slug == 'qtr'){
                    $getBeforeDate = date('Y-m-d', strtotime($contract->end_date.' - 30 days'));
                }elseif($contract->duration->slug == 'month'){
                    $getBeforeDate = date('Y-m-d', strtotime($contract->end_date.' - 10 days'));
                }

                /*if($contract->duration->slug == 'year'){
                    $endData = date('Y-m-d', strtotime($contract->end_date.' + 365 days'));
                }elseif($contract->duration->slug == 'half-year'){
                    $endData = date('Y-m-d', strtotime($contract->end_date.' + 180 days'));
                }elseif($contract->duration->slug == 'qtr'){
                    $endData = date('Y-m-d', strtotime($contract->end_date.' + 120 days'));
                }elseif($contract->duration->slug == 'month'){
                    $endData = date('Y-m-d', strtotime($contract->end_date.' + 30 days'));
                }*/
                $durationSlugs = ['year' => 365, 'half-year' => 180, 'qtr' => 120, 'month' => 30];
                $endData = date('Y-m-d', strtotime($contract->end_date. ' '. $durationSlugs[$contract->duration->slug]. 'days'));

                if($todayDate == $getBeforeDate){
                    Log::info('called Job: '.$todayDate);
                    $contract->start_date = $startDate;
                    $contract->end_date = $endData;
                    $contract->parent_id = $contract['id'];
                    $contract->contract_status_id = getStatusId(10003)->id;
                    $contract->open_ticket_contract = 1;
                    $storeContract = $this->contractRepository->storeContract($contract);
                    Log::info('New Contract Id: '.$storeContract['id']);
                    $types['contract_type_id'] = [];
                    foreach($contract->contractServicesTypes as $type){
                        array_push($types['contract_type_id'],$type->contract_type_id);
                    }
                    $this->contractRepository->storeContractService($storeContract['id'],$types);

                    /*$productTypes['contract_product_service_id'] = [];
                    foreach($contract->productService as $types){
                        $productserviceType =  [
                            'product_service_id'=>$types->product_service_id,
                            'product_qty'=>$types->product_qty,
                            'product_amount'=>$types->product_amount,
                        ];
                        array_push($productTypes['contract_product_service_id'],$productserviceType);
                    }*/
                    $productTypes = $contract->productService->map(function ($types) {
                        return [
                            'product_service_id' => $types->product_service_id,
                            'product_qty' => $types->product_qty,
                            'product_amount' => $types->product_amount,
                        ];
                    })->toArray();
                    $this->contractRepository->storeContractProductService($storeContract['id'],$productTypes);
                    $customerLocation = CustomerLocations::where('customer_id',$contract['customer_id'])->first();
                    $sendMailEmails = User::whereNot('role_id',3)->get();
                    foreach($sendMailEmails as $user){
                        $mailData = [
                            'contract' => $contract,
                            'auth_name' => 'Sarah1 Danforth1',
                            'customer_location' => $customerLocation,
                            'customer_name' => Customers::select('first_name','last_name')->where('id',$contract['customer_id'])->first(),
                            'customer_phone' => CustomerPhones::select('phone')->where(['customer_id'=>$contract['customer_id'],'is_primary',1])->first(),
                            'contract_detail_url' => 'd',
                            'user_name' => $user['first_name'] .' '. $user['last_name']
                        ];
                        Mail::to($user['email'])->send(new SendContractReNewEmailNotification($mailData));
                    }
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
