<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use phpseclib3\Crypt\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ownerData = [
          'first_name' => 'Admin',
          'last_name' => 'ad',
          'email' => 'admin1@gmail.com',
          'password' => app('hash')->make('123456'),
          //'user_type' => 'owner',
          'phone' => '865-686-4701',
          'company_name' => 'World Radio',
          'address' => '2447 Hardman Road',
          'area' => 'South Burlington',
          'zipcode' => '145263',
          'city' => 'Ahmedabad',
          'state' => 'Gujarat',
          'country' => 'India',
          'is_active' => 1,
          'is_verified' => 1,
        ];
        User::create($ownerData);
    }
}
