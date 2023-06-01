<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userPermission = config('constant.USER_PERMISSIONS');
        foreach ($userPermission as $permission){
            $checkPermissionExist = Permission::where('permission_slug',$permission['permission_slug'])->exists();
            if(!$checkPermissionExist){
                Permission::create($permission);
            }
        }
    }
}
