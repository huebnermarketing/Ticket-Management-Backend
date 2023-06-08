<?php

namespace App\Repositories\Customer;

use App\Models\CustomerLocations;
use App\Models\CustomerPhones;
use App\Models\Customers;
use App\Repositories\Customer\CustomerRepositoryInterface;
class CustomerRepository implements CustomerRepositoryInterface
{
    public function storeCustomer($data)
    {
        $customer['first_name'] = $data['first_name'];
        $customer['last_name'] = $data['last_name'];
        $customer['email'] = $data['email'];
        $createCustomer = Customers::create($customer);

        $customerId = $createCustomer['id'];
        $this->createPhone($data['primary_mobile'], $customerId,1);
        if(array_key_exists('alternate_mobile',$data) && count($data['alternate_mobile']) > 0){
            foreach($data['alternate_mobile'] as $phone){
                $this->createPhone($phone, $customerId,0);
            }
        }
        foreach ($data['addresses'] as $address){
            $address['customer_id'] = $customerId;
            CustomerLocations::create($address);
        }
        return $customer;
    }

    public function createPhone($phone, $customerId,$is_primary){
        $phonePayload['customer_id'] = $customerId;
        $phonePayload['phone'] = $phone;
        $phonePayload['is_primary'] = $is_primary;
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
}
