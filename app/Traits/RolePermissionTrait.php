<?php

namespace App\Traits;
use Spatie\Permission\Models\Role;

trait RolePermissionTrait
{
    public function getUserRole($id)
    {
       return Role::find($id);
    }
}
