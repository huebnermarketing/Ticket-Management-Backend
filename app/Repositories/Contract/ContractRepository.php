<?php

namespace App\Repositories\Contract;
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
use App\Models\CustomerContract;
use App\Filters\CustomerFilter;
use App\RestResource\RestResponse;
use Pricecurrent\LaravelEloquentFilters\EloquentFilters;
use App\Filters\ContractStatusFilter;

class ContractRepository implements ContractRepositoryInterface
{
    public function getContracts($request){
        $type = ($request['is_active'] == 'Active') ? '1' : '0';
        $customers = Customers::withCount(['contract' => function($query) use($type){
            $query->where('is_active',$type);
        }])->having('contract_count', '>', 0)->orderBy('first_name','asc')->paginate(config('constant.PAGINATION_RECORD'));
        $data['list'] = $customers;
        $data['active_contract'] = Contract::where('is_active','1')->count();
        $data['paid_amount'] = Contract::where('is_active','1')->sum('amount');
        $data['remaining_amount'] = Contract::where('is_active','1')->sum('remaining_amount');
        $data['open_contract_ticket'] = Tickets::with(['contract' => function($query){
            $query->where('is_active','1');
        }])->where(['ticket_type'=>'contract'])->whereNot('ticket_status_id','4')->count();
        return $data;
    }

    public function getClientContracts($request = null){
        $customerData = Customers::where('id',$request['customer_id'])->first();
        if(!empty($customerData)) {
            $filterQuery = EloquentFilters::make([new ContractStatusFilter($request)]);
            $contractList = Contract::filter($filterQuery)->orderBy('id', 'asc')->get();
            $data['contract_list'] = $contractList;
            $data['client_name'] = $customerData['first_name'] . ' ' . $customerData['last_name'];
            $data['client_total_active_contract'] = $contractList->count();
            $data['active_contract_amount'] = $contractList->sum('amount');
            $data['remaining_amount'] = $contractList->sum('remaining_amount');
            $data['open_contract_ticket'] = Tickets::where(['customer_id' => $request['customer_id'], 'ticket_type' => 'contract'])->whereNot('ticket_status_id', 4)->count();
            return $data;
        }else{
            return 'customer contract not found';
        }
    }

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

    public function storeContract($data){
        $contractPayload = [
            'unique_id' => $data['unique_id'],
            'customer_id' => $data['customer_id'],
            'customer_location_id' => $data['customer_location_id'],
            'contract_title' => $data['contract_title'],
            'contract_details' => $data['contract_details'],
            'amount'=>$data['amount'],
            'remaining_amount'=>$data['amount'],
            'duration_id' => $data['duration_id'],
            'payment_term_id' => $data['payment_term_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_auto_renew' => 1,
            'is_active' => 1,
            'is_archive' => 0,
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
        foreach($data['product_service_id'] as $service){
            $contractProductServicePayload = [
                'contract_id' => $productServiceId,
                'product_service_id' => $service['service_id'],
                'product_qty'=>$service['qty'],
                'product_amount'=> $service['product_amount']
            ];
            $productTypes = ContractProductService::create($contractProductServicePayload);
        }
        return $productTypes;
    }

    public function storeContractCostomer($contractId, $customerId){
        $contractCostomerPayload = [
            'contract_id'=>$contractId,
            'customer_id' => $customerId
        ];
        return CustomerContract::create($contractCostomerPayload);
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


}
