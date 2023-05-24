<?php

namespace Database\Seeders;

use App\Models\Role;
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
        $roles = [
            ['name'=>'Owner','slug'=>'owner','display_name'=>'Owner'],
            ['name'=>'Admin','slug'=>'admin','display_name'=>'Admin'],
            ['name'=>'Staff','slug'=>'staff','display_name'=>'Staff'],
        ];
        foreach ($roles as $role){
            $chekRoleExists = Role::where('slug',$role['slug'])->exists();
            if(!$chekRoleExists){
                info('not exists');
                Role::create($role);
            }else{
                info('exists');
            }
        }
    }
}
