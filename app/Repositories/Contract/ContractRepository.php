<?php

namespace App\Repositories\Contract;
use App\Http\Controllers\Api\v1\Contract\InvoiceController;
use App\Models\ContractDuration;
use App\Models\ContractPaymentTerm;
use App\Models\ContractType;
use App\Models\CustomerLocations;
use App\Models\Customers;
use App\Models\ProductServices;
use App\Models\Tickets;
use App\Repositories\Contract\ContractRepositoryInterface;
use App\Models\Contract;
use App\Models\ContractServiceType;
use App\Models\ContractProductService;
use App\Filters\CustomerFilter;
use Pricecurrent\LaravelEloquentFilters\EloquentFilters;
use App\Filters\ContractStatusFilter;

class ContractRepository implements ContractRepositoryInterface
{
    public function getContractDetails(){
//        $customers = Customers::join('customer_phones', 'customer_phones.customer_id', 'customers.id')
//            ->where('customer_phones.is_primary',1)->select('customers.*','customer_phones.customer_id','customer_phones.phone')->get();
//        $data['customers'] = $customers;

        $contractType = ContractType::select('id','contract_name')->get();
        $data['contract_services'] = $contractType;

        $productService = ProductServices::select('id','service_name')->get();
        $data['product_services'] = $productService;

        $contractDuration = ContractDuration::select('id','slug','display_name')->get();
        $data['contract_duration'] = $contractDuration;

        $contractPaymentTerms = ContractPaymentTerm::select('id','slug','display_name')->get();
        $data['contract_payment_terms'] = $contractPaymentTerms;
        return $data;
    }

    public function getContracts($request){
        $type = ($request['type'] == 'Active') ? [1] : [2,3,4];
        $customers = Customers::withCount(['contract' => function($query) use($type){
            $query->whereIn('contract_status_id',$type);
        }])->having('contract_count', '>', 0)->orderBy('first_name','asc')->paginate(config('constant.PAGINATION_RECORD'));
        return $customers;
    }

    public function clientListDashboard(){
        $contracts = Contract::where('contract_status_id',getStatusId(10001)->id);
        $data['active_contract'] = $contracts->count();
        $data['paid_amount'] = $contracts->sum('amount');
        $data['remaining_amount'] = $contracts->sum('remaining_amount');
        $data['open_contract_ticket'] = Tickets::with(['contract' => function($query){
            $query->where('contract_status_id',getStatusId(10001)->id);
        }])->where(['ticket_type'=>'contract'])->whereNot('ticket_status_id','4')->count();
        return $data;
    }

    public function getClientContracts($request = null){
        $customerData = Customers::where('id',$request['customer_id'])->first();
        if(!empty($customerData)) {
            $filterQuery = EloquentFilters::make([new ContractStatusFilter($request)]);
            $contractList = Contract::filter($filterQuery)->orderBy('id', 'asc')->get();
            $data['contract_list'] = $contractList;
            $contractDashboard = [
                'client_name'=> $customerData['first_name'] . ' ' . $customerData['last_name'],
                'client_total_active_contract' => $contractList->count(),
                'active_contract_amount' => $contractList->sum('amount'),
                'remaining_amount' => $contractList->sum('remaining_amount'),
                'open_contract_ticket' => Tickets::where(['customer_id' => $request['customer_id'], 'ticket_type' => 'contract'])->whereNot('ticket_status_id', 4)->count()
            ];
            $data['contract_dashboard'] = $contractDashboard;
            return $data;
        }else{
            return false;
        }
    }

    public function viewContract($data){

        $contractDetails = Contract::with([
            'customers' => function ($qry) {
                $qry->with('phones', function ($query){
                    $query->where("is_primary",1);
                    $query->select('id','customer_id','phone','is_primary');
                });
                $qry->select('id','first_name','last_name','email');
            },'customerLocation:id,customer_id,company_name,address_line1,area,zipcode,city,state,country',
            'contractServicesTypes' => function ($request) {
                $request->with('contractTypes:id,contract_name')
                    ->select('id','contract_id','contract_type_id');
            },'productService' => function($query){
                $query->with('productService:id,unique_id,service_name')
                ->select('id','contract_id','product_service_id','product_qty','product_amount');
            },'duration:id,slug,display_name','paymentTerm:id,slug,display_name'
            ])->select('id','unique_id','customer_id','customer_location_id','contract_title','contract_details','amount','duration_id',
            'payment_term_id','contract_status_id','start_date','end_date','is_auto_renew','open_ticket_contract','is_suspended')
            ->where('id',$data['contract_id'])->first();

        /*suspend button logic
        first check contract active or archive
        then check contract ticket all are closed*/
        $contractstatus = Contract::where(['id' => $data['contract_id'],'contract_status_id'=>getStatusId(10001)->id])->first();
        if($contractstatus != null){
            $checkTicketStatus = Tickets::with('ticket_status')
                ->whereHas('ticket_status', function($qry){
                    $qry->whereNot('unique_id',10004);
                })->where(['contract_id'=> $data['contract_id']])->whereNull('deleted_at')->count();
            $isSuspended = ($checkTicketStatus == 0) ? true : false;
        }else{
            $isSuspended = false;
        }
        $contractData['suspend_flag'] = $isSuspended;
        $contractData['contract_details'] = $contractDetails;
        return $contractData;
    }

    public function storeContract($data){
        $statusId = ($data['contract_status_id'] != null) ? $data['contract_status_id'] : getStatusId(10001)->id;
        $contractPayload = [
            'parent_id' => array_key_exists('parent_id',$data->toArray()) ? $data['parent_id'] : null,
            'customer_id' => $data['customer_id'],
            'customer_location_id' => $data['customer_location_id'],
            'contract_title' => $data['contract_title'],
            'contract_details' => $data['contract_details'],
            'amount'=>$data['amount'],
            'remaining_amount'=>$data['amount'],
            'duration_id' => $data['duration_id'],
            'payment_term_id' => $data['payment_term_id'],
            'contract_status_id' => $statusId,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_auto_renew' => 1,
            'open_ticket_contract' => 1,
            'is_suspended' => 0
        ];
        return Contract::create($contractPayload);
    }

    public function storeContractService($contractId,$data){
        foreach($data['contract_type_id'] as $type){
            $contractServicePayload = [
                'contract_id' => $contractId,
                'contract_type_id' => $type,
            ];
            $types = ContractServiceType::create($contractServicePayload);
        }
        return $types;
    }

    public function storeContractProductService($productServiceId, $data){
        foreach($data['contract_product_service_id'] as $service){
            $contractProductServicePayload = [
                'contract_id' => $productServiceId,
                'product_service_id' => $service['product_service_id'],
                'product_qty'=>$service['product_qty'],
                'product_amount'=> $service['product_amount']
            ];
            $productTypes = ContractProductService::create($contractProductServicePayload);
        }
        return $productTypes;
    }

    public function getSearchClient($data){
        $filters = EloquentFilters::make([new CustomerFilter($data)]);
        $clients = Customers::filter($filters)->get();
        return $clients;
    }

    public function archiveNotarchiveContract($data){
        $archive = ($data['archive'] == 'yes') ? 1 : 0;
        $contractData = Contract::where(['id'=> $data['contract_id'], 'customer_id'=>$data['customer_id']])->update(['is_archive'=>$archive]);
        return $contractData;
    }

    public function updateContract($data){
        $checkContract = Contract::find($data['contract_id']);
        $isAmountChanged = ($checkContract['amount'] != $data['amount']) ? 1 : 0;
        if(!empty($checkContract)){
            $updateContract = $checkContract->update([
                'contract_title'=>$data['contract_title'],
                'contract_details'=>$data['contract_details'],
                'amount'=>$data['amount'],
                'is_auto_renew'=>$data['is_auto_renew']
            ]);
            $checkContract->manyServiceType()->sync($data['contract_type_id']);
            $checkContract->contractProductServices()->sync($data['contract_product_service_id']);
            $response['is_updated'] = $updateContract;
            $response['is_amount_change'] = $isAmountChanged;
            return $response;
        }else{
            $response['is_updated'] = false;
            return $response;
        }
    }

    public function suspendContract($data){
        $checkActiveContract = Contract::where(['contract_status_id'=>getStatusId(10001)->id,'id'=>$data['contract_id']])->first();
        if(!empty($checkActiveContract)){
            $checkTicketStatus = Tickets::with('ticket_status')
                ->whereHas('ticket_status', function($qry){
                $qry->whereNot('unique_id',10004);
            })->where(['contract_id'=> $data['contract_id']])->whereNull('deleted_at')->count();
            if($checkTicketStatus == 0){
                return Contract::where('id',$data['contract_id'])->update(['is_suspended'=>1,'contract_status_id'=>getStatusId(10004)->id]);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
