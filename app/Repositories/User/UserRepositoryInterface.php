<?php
namespace App\Repositories\User;

Interface UserRepositoryInterface{
    public function allUsers();
    public function getUserWithRole($filters);
    public function storeUser($data);
    public function findUser($id);
    public function findUserWithRole($id);
    public function updateUser($data, $id);
    public function destroyUser($id);
}
