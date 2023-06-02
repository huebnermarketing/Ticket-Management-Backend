<?php

namespace Database\Seeders;

use App\Models\CompanySettings;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companySetting = CompanySettings::create([
            'company_name' => 'Systune Systems Services',
            'address_line1' => '66, management enclave Shopping Center, Opp. Indraprasth bunglow',
            'area' => 'Vastrapur',
            'zipcode' => '380015',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'country' => 'India',
            'currency' => 'INR'
        ]);

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
    }
}
