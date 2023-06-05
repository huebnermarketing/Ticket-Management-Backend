<?php

namespace App\Traits;
use App\Models\User;
use Spatie\Permission\Models\Role;

trait RolePermissionTrait
{
    public function getUserRole($id)
    {
       return Role::find($id);
    }

    public function removeExistingRole($userId){
        $findUser = User::find($userId);
        if(!empty($findUser)){
            $getExistingRole = Role::find($findUser['role_id']);
            $removeExistingRole  = $findUser->removeRole($getExistingRole);
            return true;
        }else{
            return false;
        }
    }

    public function assignRoleToUser($userId,$roleId){
        $getRole = $this->getUserRole($roleId);
        $findUser = User::find($userId);
        $assignRole = $findUser->assignRole($getRole);
        return ($assignRole) ? true :false;
    }
}
