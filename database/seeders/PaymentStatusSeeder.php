<?php

namespace Database\Seeders;

use App\Models\PaymentTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentType = ['Unpaid','Partially Paid','Paid'];
        $clientPaymentType = ['Paid','Unpaid'];

        $getSeederType = config('constant.SEEDER_TYPE');
        $paymentTypes = ($getSeederType == 'owner') ? $paymentType : $clientPaymentType;

        foreach ($paymentTypes as $type){
            $chekExists = PaymentTypes::where('payment_type',$type)->exists();
            if(!$chekExists){
                $payment['payment_type'] = $type;
                PaymentTypes::create($payment);
            }
        }

    }
}
