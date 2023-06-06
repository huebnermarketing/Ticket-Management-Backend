<?php

namespace App\Http\Controllers\Api\v1\Settings;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\Request;
use Validator;
use RestResponse;
class ContractTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $getAllContracts = ContractType::all();
            if(empty($getAllContracts)){
                return RestResponse::warning('No any Contract found.');
            }
            return RestResponse::success($getAllContracts,'Contract type list retrieve successfully.');
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
            $validate = Validator::make($request->all(), [
                'contract_name' => 'required|unique:contract_types,contract_name,NULL,id,deleted_at,NULL'
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }
            $contract['contract_name'] = $request['contract_name'];
            $createContract = ContractType::create($contract);
            if(!$createContract){
                return RestResponse::warning('Contract create failed.');
            }
            return RestResponse::success([], 'Contract created successfully.');
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
            $getContract = ContractType::find($id);
            if(empty($getContract)){
                return RestResponse::warning('Contract type not found.');
            }
            return RestResponse::success($getContract,'Contract retrieve successfully.');
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
            $getContractType = ContractType::find($id);
            if (empty($getContractType)) {
                return RestResponse::warning('Contract type not found.');
            }
            $getContractType->delete();
            return RestResponse::Success([],'Contract type deleted successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
