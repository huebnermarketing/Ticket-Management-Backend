<?php

namespace Database\Seeders;

use App\Models\CompanySettings;
use App\Models\User;
use App\Traits\RolePermissionTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use phpseclib3\Crypt\Hash;
use Spatie\Permission\Models\Role;

class OwnerSeeder extends Seeder
{
    use RolePermissionTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ownerData = [
          'first_name' => 'Owner',
          'email' => 'owner@gmail.com',
          'password' => app('hash')->make('123456'),
          'phone' => '9879205700 ',
          'company_name' => 'Systune Systems Services',
          'address' => '66, management enclave Shopping Center, Opp. Indraprasth bunglow',
          'area' => 'Vastrapur',
          'zipcode' => '380015',
          'city' => 'Ahmedabad',
          'state' => 'Gujarat',
          'country' => 'India',
          'is_active' => 1,
          'is_verified' => 1,
          'role_id' => 1
        ];
        $createUser = User::create($ownerData);
        info('$createUser---');
        info($createUser);

       /* $this->assignRoleToUser($createUser['id'],$createUser['role_id']);

        $addCompanySetting = CompanySettings::create([
            'user_id'=>$createUser['id'],
            'company_name'=>$createUser['company_name']
        ]);
        info('$addCompanySetting---');
        info($addCompanySetting);*/
    }
}
