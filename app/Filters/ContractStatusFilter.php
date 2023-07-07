<?php

namespace App\Filters;
use App\Models\Contract;
use App\Models\ContractStatus;
use Illuminate\Database\Eloquent\Builder;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;

class ContractStatusFilter extends AbstractEloquentFilter{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder): Builder{
        $contractQuery = $builder->select('id','unique_id','customer_id','contract_title','customer_location_id','start_date','end_date','amount','is_auto_renew','remaining_amount')
            ->where(['customer_id'=>$this->request['customer_id'],'open_ticket_contract'=>1]);
        $contractQuery = $contractQuery->withCount(['tickets' => function($qry){
            $qry->where("ticket_type",'contract');
        },'tickets AS open_tickets' => function($query){
            $query->whereNot('ticket_status_id',4);
            $query->where("ticket_type",'contract');
        }]);
        $contractList = $contractQuery->with(['customerLocation:id,customer_id,company_name,area',
            'contractServicesTypes' => function ($request) {
                $request->with('contractTypes:id,contract_name')
                    ->select('id','contract_id','contract_type_id');
            },]);
        if(isset($this->request->status_type)){
            $statusId = ContractStatus::where('status_name',$this->request['status_type'])->first();
            $contractList = $contractList->where('contract_status_id',$statusId['id']);
        }
        return $contractList;
    }
}
