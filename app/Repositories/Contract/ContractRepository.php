<?php

namespace App\Repositories\Contract;
use App\Models\Customers;
use App\Models\Tickets;
use App\Repositories\Contract\ContractRepositoryInterface;
use App\Models\Contract;
use App\Models\ContractServiceType;
use App\Models\ContractProductService;
use App\Models\customerContract;
use App\Filters\CustomerFilter;
use Pricecurrent\LaravelEloquentFilters\EloquentFilters;

class ContractRepository implements ContractRepositoryInterface
{
    public function getContracts(){
        $customers = Customers::withCount(['contract' => function($query){
            $query->where('is_active','1');
        }])->orderBy('first_name','asc')->paginate(config('constant.PAGINATION_RECORD'));
        $data['list'] = $customers;
        $data['active_contract'] = Contract::where('is_active','1')->count();
        $data['paid_amount'] = Contract::where('is_active','1')->sum('amount');
        $data['remaining_amount'] = Contract::where('is_active','1')->sum('amount');
        $data['open_contract_ticket'] = Tickets::with(['contract' => function($query){
            $query->where('is_active','1');
        }])->where(['ticket_status_id'=>1, 'ticket_type'=>'contract'])->count();
        return $data;
    }

    public function getClientContracts($data){
        $contractsList = Contract::
            select('id','customer_id','contract_title','customer_location_id','start_date','end_date','amount','remaining_amount')
            ->with(['customerLocation:id,customer_id,company_name,address_line1,area,zipcode,city,state,country,is_primary',
                'contractServicesTypes' => function ($query) {
                $query->with('contractTypes:id,contract_name')
                    ->select('id','contract_id','contract_type_id');
            }])
            ->where('customer_id',$data['customer_id'])->orderBy('id','asc')->paginate(config('constant.PAGINATION_RECORD'));
        return $contractsList;
    }

    public function storeContract($data){
        $contractPayload = [
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
                'product_amount'=> $service['amount']
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
        return customerContract::create($contractCostomerPayload);
    }

    public function getSearchClient($data){
        $filters = EloquentFilters::make([new CustomerFilter($data)]);

        $clients = Customers::filter($filters)->get();
        return $clients;
    }
}
