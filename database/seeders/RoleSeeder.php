<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config('constant.USER_ROLES');
        foreach ($roles as $role){
            $chekRoleExists = Role::where('name',$role['name'])->exists();
            if(!$chekRoleExists){
                Role::create($role);
            }
        }
    }
}
