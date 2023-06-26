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
        $contractQuery = $builder->select('id','customer_id','contract_title','customer_location_id','start_date','end_date','amount','is_auto_renew','remaining_amount')
            ->where(['customer_id'=>$this->request['customer_id'],'is_active'=>1,'is_archive'=>0]);
        $contractList = $contractQuery->with(['customerLocation:id,customer_id,company_name,address_line1,area,zipcode,city,state,country,is_primary',
            'contractServicesTypes' => function ($request) {
                $request->with('contractTypes:id,contract_name')
                    ->select('id','contract_id','contract_type_id');
            },'customers']);

        if(isset($this->request->status_type)){
            $statusId = ContractStatus::where('status_name',$this->request['status_type'])->first();
            $contractList = $contractList->where('contract_status_id',$statusId);
        }
        return $contractList;
    }
}
