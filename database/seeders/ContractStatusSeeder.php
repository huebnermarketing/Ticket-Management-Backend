<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContractStatus;

class ContractStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = config('constant.CONTRACT_STATUS');
        foreach ($statuses as $status){
            $chekstatusExists = ContractStatus::where('status_name',$status['status_name'])->exists();
            if(!$chekstatusExists){
                ContractStatus::create($status);
            }
        }
    }
}
