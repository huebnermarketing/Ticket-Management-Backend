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
        $companySetting = CompanySettings::updateOrCreate($setting);

        if (!User::where('email', 'owner@gmail.com')->exists()) {
            $ownerData = [
                'first_name' => 'Sarah1',
                'last_name' => 'Danforth1',
                'email' => 'owner@gmail.com',
                'password' => app('hash')->make('123456'),
                'phone' => '9879205701',
                'is_active' => 1,
                'is_verified' => 1,
                'role_id' => 1
            ];
            $createUser = User::create($ownerData);
            $getRole = Role::where('name', 'owner')->first();
            $createUser->assignRole($getRole);
        }
    }
}
