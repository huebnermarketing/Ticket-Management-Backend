<?php

namespace App\Repositories\User;

use App\Repositories\User\UserRepositoryInterface;
use App\Models\User;
use App\Traits\RolePermissionTrait;
use Spatie\Permission\Models\Role;
class UserRepository implements UserRepositoryInterface
{
    use RolePermissionTrait;

    public function allUsers()
    {
        return User::latest()->all();
    }
    public function getUserWithRole($filters = null)
    {
        $sortValue = (!empty($filters) && array_key_exists('sort_value',$filters) && !empty($filters['sort_value'])) ? $filters['sort_value'] : 'email';
        $orderBy = (!empty($filters) && array_key_exists('order_by',$filters)) && !empty($filters['order_by']) ? $filters['order_by'] : 'DESC';
        $pageLimit = (!empty($filters) && array_key_exists('total_record',$filters)) && !empty($filters['total_record']) ? $filters['total_record'] : config('constant.PAGINATION_RECORD');
        return User::with('role')->orderBy($sortValue,$orderBy)->paginate($pageLimit);
    }
    public function storeUser($data)
    {
         $getRole = $this->getUserRole($data['role_id']);
         $user = User::create($data);
         $user->assignRole($getRole['role_slug']);
         return $user;
    }

    public function findUser($id)
    {
        return User::find($id);
    }

    public function findUserWithRole($id)
    {
        return User::with('role')->find($id);
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
