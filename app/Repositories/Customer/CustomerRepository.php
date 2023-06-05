<?php

namespace App\Repositories\Customer;

use App\Models\CustomerLocations;
use App\Models\Customers;
use App\Repositories\Customer\CustomerRepositoryInterface;
class CustomerRepository implements CustomerRepositoryInterface
{
    public function storeCustomer($data)
    {
        $customer['first_name'] = $data['first_name'];
        $customer['last_name'] = $data['last_name'];
        $customer['email'] = $data['email'];
        $customer['phone'] = $data['primary_mobile'];
        info('$customer---');
        info($customer);
        $customer = Customers::create($customer);

        $customerId = $customer['id'];
        foreach ($data['addresses'] as $address){
            $address['user_id'] = $customerId;
            info('$address---');
            info($address);
            CustomerLocations::create($address);
        }
        return $customer;
    }
}
