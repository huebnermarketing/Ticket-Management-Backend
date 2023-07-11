<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\v1\Contract\InvoiceController;
use App\Mail\SendContractActivateEmailNotification;
use App\Models\Contract;
use App\Models\CustomerLocations;
use App\Models\Customers;
use App\Models\Tickets;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RestResponse;
use DB;

class ActiveDeactiveContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'active-deactive-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate Upcoming Contract and Close contract which are out of duration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            $this->info('~~~~~~Active Deactive Cron Start Execution ~~~~~~~~');
            Log::info('~~~~~~Active Deactive Cron Start Execution ~~~~~~~~');
            $todayDate = date('Y-m-d');
            $this->renewContract($todayDate);
            $this->changeOpenTicketContractFlag($todayDate);
            $this->closeContract();
            Log::info('~~~~~~Active Deactive Cron End Execution ~~~~~~~~');
            $this->info('~~~~~~Active Deactive Cron End Execution ~~~~~~~~');
        }catch(\Exception $e){
            $this->info($e->getMessage());
        }

    }

    public function renewContract($todayDate){
        $this->invoiceController = new InvoiceController;
        $getContracts = Contract::where(['start_date'=> $todayDate,'contract_status_id'=>getStatusId(10003)->id])->get();
        foreach($getContracts as $contract){
            Log::info('Cron run date: '.$todayDate);
            if($contract->is_auto_renew == 1){
                Contract::where('id',$contract['id'])->update(['contract_status_id'=>getStatusId(10001)->id]);
                $createInvoices = $this->invoiceController->createInvoices($contract['id']);
                $customerLocation = CustomerLocations::where('customer_id',$contract['customer_id'])->first();
                $mailData = [
                    'contract' => $contract,
                    'auth_name' => 'Sarah1 Danforth1',
                    'customer_location' => $customerLocation,
                    'customer_name' => Customers::select('first_name','last_name')->where('id',$contract['customer_id'])->first(),
                    'contract_detail_url' => 'd'
                ];
                Mail::to('owner@gmail.com')->send(new SendContractActivateEmailNotification($mailData));
                if($createInvoices){
                    return RestResponse::warning('Contract Invoices Not created.');
                }
                Log::info('successfully activate contract Id: '.$contract['id']. 'and Invoices are created');
            }else{
//                Contract::where('id',$contract->id)->delete();
                $contract->delete();
            }
        }
    }

    public function changeOpenTicketContractFlag($todayDate){
        $getActiveContracts = Contract::where('contract_status_id',getStatusId(10001)->id)->get();
        foreach ($getActiveContracts as $contract){
            $checkOpenTicketCount = Tickets::with('ticket_status')
                ->whereHas('ticket_status', function($qry){
                    $qry->whereNot('unique_id',10004);
                })->where(['contract_id'=> $contract['id']])->whereNull('deleted_at')->count();
            if($contract->remaining_amount == 0 && $checkOpenTicketCount == 0 && $contract['end_date'] <= $todayDate){
//                Contract::where('id',$contract['id'])->update(['open_ticket_contract'=>0]);
                $contract->update(['open_ticket_contract' => 0]);
            }
        }
    }

    public function closeContract(){
        $closedContracts = Contract::where('open_ticket_contract',0)->get();
        foreach($closedContracts as $contract){
            Contract::where('id',$contract['id'])->update(['contract_status_id'=>getStatusId(10002)->id]);
        }
    }
}
