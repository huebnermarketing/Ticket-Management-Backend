<?php

namespace App\Repositories\Customer;

use App\Models\CustomerLocations;
use App\Models\CustomerPhones;
use App\Models\Customers;
use App\Repositories\Customer\CustomerRepositoryInterface;
use RestResponse;
class CustomerRepository implements CustomerRepositoryInterface
{
    public function storeCustomer($data)
    {
        $customer = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email']
        ];
        $createCustomer = Customers::create($customer);

        $customerId = $createCustomer['id'];
        $this->createPhone($data['primary_mobile'], $customerId,1);
        if(array_key_exists('alternate_mobile',$data) && count($data['alternate_mobile']) > 0){
            foreach($data['alternate_mobile'] as $phone){
                $this->createPhone($phone, $customerId,0);
            }
        }

        if(array_key_exists('addresses',$data)){
            foreach ($data['addresses'] as $address){
                $address['customer_id'] = $customerId;
                CustomerLocations::create($address);
            }
        }
        return $createCustomer;
    }

    public function createPhone($phone, $customerId,$is_primary){
        $phonePayload = [
            'customer_id' => $customerId,
            'phone' => $phone,
            'is_primary' => $is_primary
        ];
        CustomerPhones::create($phonePayload);
    }

    public function getCustomers($filters = null)
    {
        $sortValue = (!empty($filters) && array_key_exists('sort_value',$filters) && !empty($filters['sort_value'])) ? $filters['sort_value'] : 'email';
        $orderBy = (!empty($filters) && array_key_exists('order_by',$filters)) && !empty($filters['order_by']) ? $filters['order_by'] : 'DESC';
        $pageLimit = (!empty($filters) && array_key_exists('total_record',$filters)) && !empty($filters['total_record']) ? $filters['total_record'] : config('constant.PAGINATION_RECORD');

        return Customers::select('customers.id','customers.first_name','customers.last_name',
                'customer_phones.phone','customers.email','customer_locations.company_name')
            ->join('customer_locations', 'customers.id', 'customer_locations.customer_id')
            ->join('customer_phones', 'customers.id', 'customer_phones.customer_id')
            ->where('customer_locations.is_primary',1)
            ->where('customer_phones.is_primary',1)
            ->orderBy($sortValue,$orderBy)->paginate($pageLimit);
    }

    public function findCustomer($id)
    {
        return Customers::with(['locations','phones'])->find($id);
    }

    public function updateCustomer($data,$customerId)
    {
        $getCustomer = $this->findCustomer($customerId);
        if (empty($getCustomer)) {
            return RestResponse::warning('Customer not found.');
        }
        //Update Customer
        $getCustomer = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email']
        ];
        return $getCustomer->save();
    }

    public function updatePrimaryLocations($data,$customerId)
    {
        $getCustomer = $this->findCustomer($customerId);
        if (empty($getCustomer)) {
            return RestResponse::warning('Customer not found.');
        }
        //Update Customer Primary Address
        if(array_key_exists('primary_address_id',$data)){
            foreach ($getCustomer['locations'] as $address) {
                if ($address->id === $data['primary_address_id']) {
                    $address->is_primary = true;
                } else {
                    $address->is_primary = false;
                }
                $address->save();
            }
        }
        return ;
    }

    public function updateCustomerPhones($data,$customerId)
    {
        //Delete Customer Phones
        $deletePhones = CustomerPhones::where('customer_id',$customerId)->delete();
        //Create Customer Phones
        $this->createPhone($data['primary_mobile'], $customerId,1);
        if(array_key_exists('alternate_mobile',$data->all()) && count($data['alternate_mobile']) > 0){
            foreach($data['alternate_mobile'] as $phone){
                $this->createPhone($phone, $customerId,0);
            }
        }
        return;
    }

    public function findAddress($id)
    {
        return CustomerLocations::find($id);
    }

    public function addAddress($data)
    {
        return CustomerLocations::create($data);
    }

    public function updateAddress($data,$addressId)
    {
        $getAddress = $this->findAddress($addressId);
        if (empty($getAddress)) {
            return RestResponse::warning('Customer address not found.');
        }
        $getAddress['address_line1'] = $data['address_line1'];
        $getAddress['company_name'] = $data['company_name'];
        $getAddress['area'] = $data['area'];
        $getAddress['city'] = $data['city'];
        $getAddress['zipcode'] = $data['zipcode'];
        $getAddress['state'] = $data['state'];
        $getAddress['country'] = $data['country'];
        return $getAddress->save();
    }
}
