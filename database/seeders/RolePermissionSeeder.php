<?php

namespace Database\Seeders;

//use App\Models\Role;
use AWS\CRT\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $getAdminRole = Role::where('role_slug','admin')->first();
        if(!empty($getAdminRole)){
            //$adminPermissions = ['user-auth','user-crud','company-setting','user-profile'];
            $adminPermissions = [
                config('constant.PERMISSION_USER_AUTH'),
                config('constant.PERMISSION_USER_CRUD'),
                config('constant.PERMISSION_COMPANY_SETTING'),
                config('constant.PERMISSION_USER_PROFILE'),
                config('constant.PERMISSION_CONTRACT_TYPE_CRUD'),
                config('constant.PERMISSION_PROBLEM_TYPE_CRUD'),
                config('constant.PERMISSION_TICKET_STATUS_CRUD'),
                config('constant.PERMISSION_PRODUCT_SERVICES_CRUD'),
            ];
            $getAdminRole->syncPermissions($adminPermissions);
        }

        $getOwnerRole = Role::where('role_slug','owner')->first();
        if(!empty($getOwnerRole)){
            //$ownerPermissions = ['user-auth','user-crud','company-setting','user-profile'];
            $ownerPermissions = [
                config('constant.PERMISSION_USER_AUTH'),
                config('constant.PERMISSION_USER_CRUD'),
                config('constant.PERMISSION_COMPANY_SETTING'),
                config('constant.PERMISSION_USER_PROFILE'),
                config('constant.PERMISSION_CONTRACT_TYPE_CRUD'),
                config('constant.PERMISSION_PROBLEM_TYPE_CRUD'),
                config('constant.PERMISSION_TICKET_STATUS_CRUD'),
                config('constant.PERMISSION_PRODUCT_SERVICES_CRUD'),
            ];
            $getOwnerRole->syncPermissions($ownerPermissions);
        }

        $getUserRole = Role::where('role_slug','user')->first();
        if(!empty($getUserRole)){
            //$userPermissions = ['user-auth','user-profile'];
            $userPermissions = [
                config('constant.PERMISSION_USER_AUTH'),
                config('constant.PERMISSION_USER_PROFILE'),
            ];
            $getUserRole->syncPermissions($userPermissions);
        }
    }
}
