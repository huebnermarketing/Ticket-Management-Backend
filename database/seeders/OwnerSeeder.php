<?php

namespace Database\Seeders;

use App\Models\CompanySettings;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $getCurrency = Currency::where('code','INR')->first();
        $setting['company_name'] = 'Systune Systems Services';
        $setting['address_line1'] = '66, management enclave Shopping Center, Opp. Indraprasth bunglow';
        $setting['area'] = 'Vastrapur';
        $setting['zipcode'] = '380015';
        $setting['city'] = 'Ahmedabad';
        $setting['state'] = 'Gujarat';
        $setting['country'] = 'India';
        $setting['currency_id'] = (!empty($getCurrency)) ? $getCurrency['id'] : null;
        $companySetting = CompanySettings::create($setting);

        $ownerData = [
          'first_name' => 'Sarah',
          'last_name' => 'Danforth',
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
        $getRole = Role::where('name','owner')->first();
        $createUser->assignRole($getRole);
    }
}
