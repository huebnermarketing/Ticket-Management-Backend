<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CustomerLocations;
use App\Models\CustomerPhones;
use App\Models\Customers;
use App\Repositories\Customer\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use RestResponse;

class CustomerController extends Controller
{
    private $customerRepository;
    private $perCustomerCRUD;
    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->perCustomerCRUD = config('constant.PERMISSION_CUSTOMER_CRUD');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perCustomerCRUD)) {
                $filters = [
                    'total_record' => $request->total_record,
                    'order_by' => $request->order_by,
                    'sort_value' => $request->sort_value
                ];
                $getAllCustomer = $this->customerRepository->getCustomers($filters);
                if(empty($getAllCustomer)){
                    return RestResponse::warning('Customer not found.');
                }
                return RestResponse::Success($getAllCustomer, 'Customers retrieve successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        } catch (\Exception $e) {
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
           if(Auth::user()->hasPermissionTo($this->perCustomerCRUD)) {
               DB::beginTransaction();
               $validate = Validator::make($request->all(), [
                   'first_name' => 'required',
                   'email' => 'required|email',
                   'primary_mobile' => 'required',
                   'addresses.*.address_line1' => 'required',
                   'addresses.*.company_name' => 'required',
                   'addresses.*.area' => 'required',
                   'addresses.*.city' => 'required',
                   'addresses.*.zipcode' => 'required|min:4|max:8',
                   'addresses.*.country' => 'required',
                   'addresses.*.state' => 'required',
                   'addresses.*.is_primary' => 'required',
               ]);
               if ($validate->fails()) {
                   return RestResponse::validationError($validate->errors());
               }

               $storeCustomer = $this->customerRepository->storeCustomer($request->all());
               if(!$storeCustomer){
                   return RestResponse::warning('Customer create failed.');
               }
               DB::commit();
               return RestResponse::Success([],'Customer created successfully.');
           }else {
               return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
           }
       }catch (\Exception $e) {
           DB::rollBack();
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
            if(Auth::user()->hasPermissionTo($this->perCustomerCRUD)) {
                if(empty($id)){
                    return RestResponse::warning('Id not found. Must pass in URL.');
                }
                $getCustomer = Customers::where(['id' => $id])->with(['locations','phones'=> function($qry){
                    $qry->where('is_primary',0);
                }])->first();
                if(empty($getCustomer)){
                    return RestResponse::warning('Customer not found.');
                }
                $collection = CustomerPhones::where(['customer_id'=>$id,'is_primary'=>1])->first();
                $getCustomer['primary_mobile'] = $collection['phone'];
                return RestResponse::Success($getCustomer,'Customer retrieve successfully.');
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
        try {
            if(Auth::user()->hasPermissionTo($this->perCustomerCRUD)) {
                DB::beginTransaction();
                $validate = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'email' => 'required|email',
                    'primary_mobile' => 'required',
                    'primary_address_id' => 'required'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }

                $updateCustomer = $this->customerRepository->updateCustomer($request,$id);
                $updateCustomerPhones = $this->customerRepository->updateCustomerPhones($request,$id);
                $updatePrimaryLocations = $this->customerRepository->updatePrimaryLocations($request,$id);
                DB::commit();
                return RestResponse::Success([],'Customer updated successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            DB::rollBack();
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
            if(Auth::user()->hasPermissionTo($this->perCustomerCRUD)) {
                $customer = $this->customerRepository->findCustomer($id);
                if (empty($customer)) {
                    return RestResponse::warning('Customer not found.');
                }
                $customer->delete();
                return RestResponse::Success([],'Customer deleted successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function searchCustomer(Request $request)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perCustomerCRUD)) {
                $validate = Validator::make($request->all(), [
                    'search_text' => 'required'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $limit = isset($request->total_record) ? $request->total_record : config('constant.PAGINATION_RECORD');
                $searchCustomer = Customers::join('customer_phones', 'customer_phones.customer_id', 'customers.id')
                    ->where('customer_phones.is_primary',1)
                    ->where(function ($qry) use($request){
                        $qry->where('customers.first_name', 'LIKE', '%' . $request->search_text . '%');
                        $qry->orWhere('customers.last_name', 'LIKE', '%' . $request->search_text . '%');
                        $qry->orWhere('customers.email', 'LIKE', '%' . $request->search_text . '%');
                        $qry->orWhere('customer_phones.phone', 'LIKE', '%' . $request->search_text . '%');
                    })
                    ->select('customers.*','customer_phones.customer_id','customer_phones.phone')
                    ->paginate($limit);
                if(count($searchCustomer) < 0){
                    return RestResponse::warning('No any search result found.');
                }
                return RestResponse::Success($searchCustomer,'Customer search successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function addCustomerAddress(Request $request)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perCustomerCRUD)) {
                $validate = Validator::make($request->all(), [
                    'address_line1' => 'required',
                    'company_name' => 'required',
                    'area' => 'required',
                    'city' => 'required',
                    'zipcode' => 'required|min:4|max:8',
                    'state' => 'required',
                    'country' => 'required',
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $addAddress = $this->customerRepository->addAddress($request->all());
                if(!$addAddress){
                    return RestResponse::warning('Customer address create failed.');
                }
                return RestResponse::Success([],'Customer address added successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function getCustomerAddress($customerId){
        try{
            $getCustomer = CustomerLocations::where('id',$customerId)->first();
            if(empty($getCustomer)){
                return RestResponse::warning('No any customer address found.');
            }
            return RestResponse::Success($getCustomer,'Customer address retrieve successfully.');
        }catch (\Exception $e) {
        }
        return RestResponse::error($e->getMessage(), $e);
    }

    public function updateCustomerAddress(Request $request,$id){
        try{
            if(Auth::user()->hasPermissionTo($this->perCustomerCRUD)) {
                $validate = Validator::make($request->all(), [
                    'address_line1' => 'required',
                    'company_name' => 'required',
                    'area' => 'required',
                    'city' => 'required',
                    'zipcode' => 'required|min:4|max:8',
                    'state' => 'required',
                    'country' => 'required'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $addAddress = $this->customerRepository->updateAddress($request->all(),$id);
                if(!$addAddress){
                    return RestResponse::warning("You can't update the primary address.");
                }
                return RestResponse::Success([],'Customer address updated successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function deleteCustomerAddress($id){
        try{
            if(Auth::user()->hasPermissionTo($this->perCustomerCRUD)) {
                $customer = $this->customerRepository->findAddress($id);
                if (empty($customer)) {
                    return RestResponse::warning('Customer not found.');
                }
                if($customer->is_primary == 1){
                    return RestResponse::warning("You can't delete the primary address.");
                }
                $customer->delete();
                return RestResponse::Success([],'Customer deleted successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
