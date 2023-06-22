<?php

namespace App\Http\Controllers\Api\v1\Contract;

use App\Http\Controllers\Controller;
use App\Repositories\Contract\ContractRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use RestResponse;

class ContractController extends Controller
{
    private $contractRepository;
    public function __construct(ContractRepositoryInterface $contractRepository)
    {
        $this->contractRepository = $contractRepository;
        $this->perContractCRUD = config('constant.PERMISSION_CONTRACT_TYPE_CRUD');
    }
    public function store(Request $request){
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)) {
                DB::beginTransaction();
                $validate = Validator::make($request->all(), [
                    'customer_id' => 'required',
                    'customer_location_id' => 'required',
                    'contract_title' => 'required',
                    'contract_details' => 'required',
                    'amount' => 'required',
                    'duration_id' => 'required',
                    'payment_term_id' => 'required',
                    'start_date' => 'required'
                ]);

                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $storeContract = $this->contractRepository->storeContract($request);
                if(!$storeContract){
                    return RestResponse::warning('Contract Not created.');
                }
                $storeContractService = $this->contractRepository->storeContractService($storeContract['id'],$request);
                if(!$storeContractService){
                    return RestResponse::warning('Contract Service Not created.');
                }
                $storeContractProductService = $this->contractRepository->storeContractProductService($storeContract['id'],$request);
                if(!$storeContractProductService){
                    return RestResponse::warning('Contract Service Not created.');
                }
                $storeContractCostomer = $this->contractRepository->storeContractCostomer($storeContract['id'],$request->customer_id);
                DB::commit();
                return RestResponse::Success([],'Contract created successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            dd($e->getLine());
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
