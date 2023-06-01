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
            $adminPermissions = ['user-auth','user-crud','company-setting','user-profile'];
            $getAdminRole->syncPermissions($adminPermissions);
        }

        $getOwnerRole = Role::where('role_slug','owner')->first();
        if(!empty($getOwnerRole)){
            $ownerPermissions = ['user-auth','user-crud','company-setting','user-profile'];
            $getOwnerRole->syncPermissions($ownerPermissions);
        }

        $getUserRole = Role::where('role_slug','user')->first();
        if(!empty($getUserRole)){
            $userPermissions = ['user-auth','user-profile'];
            $getUserRole->syncPermissions($userPermissions);
        }
    }
}
