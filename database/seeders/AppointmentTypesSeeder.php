<?php

namespace Database\Seeders;

use App\Models\AppointmentTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appointmentType = ['On site','Our Office','Phone Call','Remote Support','aa','ccc'];
        $clientAppointmentType = ['Remote Support'];

        $getSeederType = config('constant.SEEDER_TYPE');
        $appointments = ($getSeederType == 'owner') ? $appointmentType : $clientAppointmentType;

        foreach ($appointments as $type){
            $chekExists = AppointmentTypes::where('appointment_name',$type)->exists();
            if(!$chekExists){
                $appointment['appointment_name'] = $type;
                $appointment['active_status'] = '1';
                AppointmentTypes::create($appointment);
            }
        }
    }
}
