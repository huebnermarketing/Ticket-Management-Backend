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
        $priorityType = [
            ['priority_name' => 'High', 'text_color' => '#FFFFFF', 'background_color' => '#FF1C1C'],
            ['priority_name' => 'Low', 'text_color' => '#FFFFFF', 'background_color' => '#F48200'],
            ['priority_name' => 'Medium', 'text_color' => '#FFFFFF', 'background_color' => '#48AB1A']
        ];
        $clientPriorityType = [
            ['priority_name' => 'High', 'text_color' => '#FFFFFF', 'background_color' => '#000000'],
            ['priority_name' => 'Low', 'text_color' => '#FFFFFF', 'background_color' => '#000000']
        ];

        $getSeederType = config('constant.SEEDER_TYPE');
        $priorityTypes = ($getSeederType == 'owner') ? $priorityType : $clientPriorityType;

        foreach ($priorityTypes as $type){
            $chekExists = TicketPriority::where('priority_name',$type)->exists();
            if(!$chekExists){
                $priority['priority_name'] = $type['priority_name'];
                $priority['text_color'] = $type['text_color'];
                $priority['background_color'] = $type['background_color'];
                $priority['is_active'] = 1;
                TicketPriority::create($priority);
            }
        }
    }
}
