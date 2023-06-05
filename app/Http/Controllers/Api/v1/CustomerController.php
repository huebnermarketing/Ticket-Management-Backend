<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Customer\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use RestResponse;

class CustomerController extends Controller
{
    private $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
           info('inside request---');
           info($request);
           DB::beginTransaction();
           $validate = Validator::make($request->all(), [
               'first_name' => 'required',
               'last_name' => 'required',
               'email' => 'required|email',
               'primary_mobile' => 'required',
                'addresses.*.address_line1' => 'required',
                'addresses.*.company_name' => 'required',
                'addresses.*.area' => 'required',
                'addresses.*.city' => 'required',
                'addresses.*.zipcode' => 'required',
                'addresses.*.country' => 'required',
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
