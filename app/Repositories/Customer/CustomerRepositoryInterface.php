<?php
namespace App\Repositories\Customer;

Interface CustomerRepositoryInterface{
    public function storeCustomer($data);
    public function createPhone($phone,$customerId,$is_primary);
    public function getCustomers($filters);
    public function findCustomer($id);
    public function updateCustomer($data, $customerId);

    public function addAddress($data);
    public function updateAddress($data,$addressId);
}
