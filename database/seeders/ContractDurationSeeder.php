<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContractDuration;

class ContractDurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $durations = config('constant.CONTRACT_DURATIONS');
        foreach ($durations as $duration){
            $chekDurationExists = ContractDuration::where('display_name',$duration['display_name'])->exists();
            if(!$chekDurationExists){
                ContractDuration::create($duration);
            }
        }
    }
}
