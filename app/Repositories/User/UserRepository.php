<?php

namespace App\Repositories\User;

use App\Repositories\User\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function allUsers()
    {
        return User::latest()->all();
    }
    public function getUserWithRole($filters = null)
    {
        $sortValue = (!empty($filters) && array_key_exists('sort_value',$filters) && !empty($filters['sort_value'])) ? $filters['sort_value'] : 'email';
        $orderBy = (!empty($filters) && array_key_exists('order_by',$filters)) && !empty($filters['order_by']) ? $filters['order_by'] : 'DESC';
        $pageLimit = (!empty($filters) && array_key_exists('total_record',$filters)) && !empty($filters['total_record']) ? $filters['total_record'] : 10;
        return User::with('role')->where(['is_active' => 1 ,'is_verified' =>1])->orderBy($sortValue,$orderBy)->paginate($pageLimit);
    }
    public function storeUser($data)
    {
        return User::create($data);
    }

    public function findUser($id)
    {
        return User::where(['is_active' => 1 ,'is_verified' =>1 ,'id' => $id])->find($id);
    }

    public function findUserWithRole($id)
    {
        return User::with('role')->where(['is_active' => 1 ,'is_verified' =>1 ,'id' => $id])->find($id);
    }

    public function updateUser($data, $id)
    {
        $user = User::where('id', $id)->first();
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->is_active = $data['is_active'];
        $user->role_id = $data['role_id'];
        if(array_key_exists('profile_photo',$data)){
            $user->profile_photo = $data['profile_photo'];
        }
        $user->save();
        return $user;
    }

    public function destroyUser($id)
    {
        $category = User::find($id);
        $category->delete();
    }
}
