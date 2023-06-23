<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContractPaymentTerm;

class ContractPaymentTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $terms = config('constant.CONTRACT_PAYMENT_TERM');
        foreach ($terms as $term){
            $chekTermExists = ContractPaymentTerm::where('display_name',$term['display_name'])->exists();
            if(!$chekTermExists){
                ContractPaymentTerm::create($term);
            }
        }
    }
}
