<?php

namespace App\Http\Controllers\Api\v1\Settings;

use App\Http\Controllers\Controller;
use App\Models\ProductServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use RestResponse;
class ProductServiceController extends Controller
{
    private $perProductServices;
    public function __construct()
    {
        $this->perProductServices = config('constant.PERMISSION_PRODUCT_SERVICES_CRUD');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perProductServices)) {
                $getAllService = ProductServices::all();
                if(empty($getAllService)){
                    return RestResponse::warning('Product service not found.');
                }
                return RestResponse::success($getAllService,'Product service list retrieve successfully.');
            }else {
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
            if(Auth::user()->hasPermissionTo($this->perProductServices)) {
                $validate = Validator::make($request->all(), [
                    'service_name' => 'required|unique:product_services,service_name,NULL,id,deleted_at,NULL'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $create = ProductServices::create([
                    'service_name' => $request['service_name'],

                ]);
                if(!$create){
                    return RestResponse::warning('Product service create failed.');
                }
                return RestResponse::success([], 'Product service created successfully.');
            }else {
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
            if(Auth::user()->hasPermissionTo($this->perProductServices)) {
                $getProductService = ProductServices::find($id);
                if(empty($getProductService)){
                    return RestResponse::warning('Product service not found.');
                }
                return RestResponse::success($getProductService,'Product service retrieve successfully.');
            }else {
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
            if(Auth::user()->hasPermissionTo($this->perProductServices)) {
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
            }else {
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
            if(Auth::user()->hasPermissionTo($this->perProductServices)) {
                $getProductService = ProductServices::find($id);
                if (empty($getProductService)) {
                    return RestResponse::warning('Product service not found.');
                }
                if($getProductService['is_lock'] == 1){
                    return RestResponse::warning("You can't delete default product service.");
                }
                $getProductService->delete();
                return RestResponse::Success([],'Product service deleted successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
