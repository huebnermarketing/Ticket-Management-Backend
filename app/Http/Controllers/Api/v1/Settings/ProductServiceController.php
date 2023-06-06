<?php

namespace App\Http\Controllers\Api\v1\Settings;

use App\Http\Controllers\Controller;
use App\Models\ProductServices;
use Illuminate\Http\Request;
use Validator;
use RestResponse;
class ProductServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $getAllService = ProductServices::all();
            if(empty($getAllService)){
                return RestResponse::warning('Product service not found.');
            }
            return RestResponse::success($getAllService,'Product service list retrieve successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                'service_name' => 'required|unique:product_services,service_name,NULL,id,deleted_at,NULL'
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }
            $create = ProductServices::create(['service_name' => $request['service_name']]);
            if(!$create){
                return RestResponse::warning('Product service create failed.');
            }
            return RestResponse::success([], 'Product service created successfully.');
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
        //
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
            $getProductService = ProductServices::find($id);
            if(empty($getProductService)){
                return RestResponse::warning('Product service not found.');
            }
            return RestResponse::success($getProductService,'Product service retrieve successfully.');
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
                'service_name' => 'required|unique:product_services,service_name,'.$id.'NULL,id,deleted_at,NULL'
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }

            $findService = ProductServices::find($id);
            if(empty($findService)){
                return RestResponse::warning('Product service not found.');
            }
            if($findService['is_lock'] == 1){
                return RestResponse::warning("You can't update default product service.");
            }
            $findService['service_name'] = $request['service_name'];
            $findService->save();
            return RestResponse::success([], 'Product service updated successfully.');
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
            $getProductService = ProductServices::find($id);
            if (empty($getProductService)) {
                return RestResponse::warning('Product service not found.');
            }
            if($getProductService['is_lock'] == 1){
                return RestResponse::warning("You can't delete default product service.");
            }
            $getProductService->delete();
            return RestResponse::Success([],'Product service deleted successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
