<?php

namespace App\Http\Controllers\Api\v1\Contract;

use App\Http\Controllers\Controller;
use App\Models\ContractServiceType;
use App\Models\ContractType;
use App\Repositories\Contract\ContractRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Warning;
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

    public function getDetails(){
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)) {
                $details = $this->contractRepository->getContractDetails();
                return RestResponse::Success($details,'Contracts details.');
            }
        }catch(\Exception $e){
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function index(Request $request){
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)) {
                $contractList = $this->contractRepository->getContracts($request);
                if(!$contractList){
                    return RestResponse::warning('Contract list not found.');
                }
                DB::commit();
                return RestResponse::Success($contractList,'Contracts retrieve successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch(\Exception $e){
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function store(Request $request){
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)) {
                DB::beginTransaction();
                $validate = Validator::make($request->all(), [
                    'customer_id' => 'required',
                    'customer_location_id' => 'required',
                    'contract_title' => 'required|max:50',
                    'contract_details' => 'required|max:500',
                    'amount' => 'required|numeric|gt:0',
                    'duration_id' => 'required',
                    'payment_term_id' => 'required',
                    'start_date' => 'required',
                    'contract_product_service_id.*.product_service_id' => 'required',
                    'contract_product_service_id.*.product_qty' => 'required|numeric|gt:0',
                    'contract_product_service_id.*.product_amount' => 'required|numeric|gt:0'
                ]);

                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $storeContract = $this->contractRepository->storeContract($request);
                if(!$storeContract){
                    return RestResponse::warning('Contract not created.');
                }
                $storeContractService = $this->contractRepository->storeContractService($storeContract['id'],$request);
                if(!$storeContractService){
                    return RestResponse::warning('Contract service not created.');
                }
                $storeContractProductService = $this->contractRepository->storeContractProductService($storeContract['id'],$request);
                if(!$storeContractProductService){
                    return RestResponse::warning('Contract product service not created.');
                }
//                $storeContractCostomer = $this->contractRepository->storeContractCostomer($storeContract['id'],$request->customer_id);
                DB::commit();
                return RestResponse::Success([],'Contract created successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function contractList(Request $request){
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)) {
                $clientContractList = $this->contractRepository->getClientContracts($request);
                if(!$clientContractList){
                    return RestResponse::warning('Client contract list not found.');
                }
                return RestResponse::Success($clientContractList,'Contracts retrieve successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch(\Exception $e){
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function viewContract(Request $request){
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                $viewContract = $this->contractRepository->viewContract($request);
                if(!$viewContract){
                    return RestResponse::warning('Contract not found');
                }
                return RestResponse::Success($viewContract,'Contracts retrieve successfully.');
            }else{
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }

        }catch(\Exception $e){
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function searchClient(Request $request){
         try{
             if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                 $searchClient = $this->contractRepository->getSearchClient($request);
                 if(!$searchClient){
                     return RestResponse::warning('Client not found.');
                 }
                 return RestResponse::Success($searchClient, 'Client retrieve successfully.');
             }else{
                 return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
             }
         }catch(\Exception $e){
             DB::rollBack();
             return RestResponse::error($e->getMessage(), $e);
         }
    }

    public function archiveContract(Request $request)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                $archiveContract = $this->contractRepository->archiveNotarchiveContract($request);
                if(!$archiveContract){
                    return RestResponse::warning('Contract not found.');
                }
                return RestResponse::Success($archiveContract, 'Contract archived successfully.');
            }else{
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch(\Exception $e){
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function updateContract(Request $request){
        try{
            DB::beginTransaction();
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                $validate = Validator::make($request->all(), [
                    "contract_id" => 'required',
                    'contract_product_service_id.*.product_service_id' => 'required',
                    'contract_product_service_id.*.product_qty' => 'required|numeric|gt:0',
                    'contract_product_service_id.*.product_amount' => 'required|numeric|gt:0'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $updateContract = $this->contractRepository->updateContract($request);
                if(!$updateContract){
                    return RestResponse::warning('Contract not updated.');
                }
                DB::commit();
                return RestResponse::Success($updateContract, 'Contract updated successfully.');
            }else{
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch(\Exception $e){
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function suspendContract(Request $request){
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                $suspendContract = $this->contractRepository->suspendContract($request);
                if(!$suspendContract){
                    return RestResponse::warning('Contract not suspended.');
                }
                return RestResponse::Success($suspendContract, 'Contract successfully Suspended.');
            }else{
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch(\Exception $e){
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
