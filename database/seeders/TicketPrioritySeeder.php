<?php

namespace Database\Seeders;

use App\Models\TicketPriority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $priorityType = ['High','Low','Medium'];
        $clientPriorityType = ['High','Low'];

        $getSeederType = config('constant.SEEDER_TYPE');
        $priorityTypes = ($getSeederType == 'owner') ? $priorityType : $clientPriorityType;

        foreach ($priorityTypes as $type){
            $chekExists = TicketPriority::where('priority_name',$type)->exists();
            if(!$chekExists){
                $priority['priority_name'] = $type;
                $priority['is_active'] = 1;
                TicketPriority::create($priority);
            }
        }
    }
}
