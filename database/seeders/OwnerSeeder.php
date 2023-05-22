<?php

namespace Database\Seeders;

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
        $ownerData = [
          'first_name' => '',
          'last_name' => '',
          'email' => '',
          'password' => '',
          'user_type' => 'owner',
          'phone' => '',
          'company_name' => '',
          'address' => '',
          'area' => '',
          'zipcode' => '',
          'city' => '',
          'state' => '',
          'country' => '',
          'profile_photo' => '',
          'is_active' => 1,
          'is_verified' => 1,
        ];
        User::create($ownerData);
    }
}
