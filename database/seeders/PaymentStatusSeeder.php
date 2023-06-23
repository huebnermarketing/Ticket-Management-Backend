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
        $paymentType = [
            ['payment_type' => 'Partially Paid', 'text_color' => '#FFFFFF', 'background_color' => '#FF69B5'],
            ['payment_type' => 'Paid', 'text_color' => '#FFFFFF', 'background_color' => '#B960FF'],
            ['payment_type' => 'Unpaid', 'text_color' => '#FFFFFF', 'background_color' => '#709FCE'],
            ['payment_type' => 'Uncollectible', 'text_color' => '#FFFFFF', 'background_color' => '#ECB937'],
        ];
        $clientPaymentType = [
            ['payment_type' => 'Paid', 'text_color' => '#FFFFFF', 'background_color' => '#000000'],
            ['payment_type' => 'Unpaid', 'text_color' => '#FFFFFF', 'background_color' => '#000000']
        ];

        $getSeederType = config('constant.SEEDER_TYPE');
        $paymentTypes = ($getSeederType == 'owner') ? $paymentType : $clientPaymentType;

        foreach ($paymentTypes as $type){
            $chekExists = PaymentTypes::where('payment_type',$type)->exists();
            if(!$chekExists){
                $payment['payment_type'] = $type['payment_type'];
                $payment['text_color'] = $type['text_color'];
                $payment['background_color'] = $type['background_color'];
                PaymentTypes::create($payment);
            }
        }

    }
}
