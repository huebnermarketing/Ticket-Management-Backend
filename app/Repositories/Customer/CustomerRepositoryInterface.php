<?php
namespace App\Repositories\Customer;

Interface CustomerRepositoryInterface{
//    public function allUsers();
    public function storeCustomer($data);
    public function createPhone($phone,$customerId,$is_primary);
    public function getCustomers($filters);
    public function findCustomer($id);
//    public function findUser($id);
//    public function findUserWithRole($id);
//    public function updateUser($data, $id);
//    public function destroyUser($id);
}
