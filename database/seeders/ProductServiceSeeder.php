<?php

namespace Database\Seeders;

use App\Models\ProductServices;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productServicesArray = ['Keyboard','Computer','Macbook','iMac','Router','Motherboard'];
        $clientProductServices = ['Computer','Macbook','Router'];

        $getSeederType = config('constant.SEEDER_TYPE');
        $productServices = ($getSeederType == 'owner') ? $productServicesArray : $clientProductServices;

        foreach ($productServices as $product){
            $chekExists = ProductServices::where('service_name',$product)->exists();
            if(!$chekExists){
                ProductServices::create([
                    'service_name' => $product,
                    'is_lock' => 1
                ]);
            }
        }
    }
}
