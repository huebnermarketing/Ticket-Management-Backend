<?php

namespace App\Http\Controllers\Api\v1\Settings;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use RestResponse;
class ContractTypeController extends Controller
{
    private $perContractCRUD;
    public function __construct()
    {
        $this->perContractCRUD = config('constant.PERMISSION_CONTRACT_TYPE_CRUD');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                $getAllContracts = ContractType::all();
                if(empty($getAllContracts)){
                    return RestResponse::warning('No any Contract found.');
                }
                return RestResponse::success($getAllContracts,'Contract type list retrieve successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                $validate = Validator::make($request->all(), [
                    'contract_name' => 'required|unique:contract_types,contract_name,NULL,id,deleted_at,NULL'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $createContract = ContractType::create(['contract_name' => $request['contract_name']]);
                if(!$createContract){
                    return RestResponse::warning('Contract create failed.');
                }

                return RestResponse::success($createContract, 'Contract created successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                $getContract = ContractType::find($id);
                if(empty($getContract)){
                    return RestResponse::warning('Contract type not found.');
                }
                return RestResponse::success($getContract,'Contract retrieve successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                $validate = Validator::make($request->all(), [
                    'contract_name' => 'required|unique:contract_types,contract_name,'.$id.'NULL,id,deleted_at,NULL'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }

                $findContract = ContractType::find($id);
                if(empty($findContract)){
                    return RestResponse::warning('Contract type not found.');
                }

                $findContract['contract_name'] = $request['contract_name'];
                $findContract->save();
                return RestResponse::success([], 'Contract updated successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perContractCRUD)){
                $getContractType = ContractType::find($id);
                if (empty($getContractType)) {
                    return RestResponse::warning('Contract type not found.');
                }
                $getContractType->delete();
                return RestResponse::Success([],'Contract type deleted successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
