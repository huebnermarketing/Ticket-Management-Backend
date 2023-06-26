<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ticketStatusType = [
            ['status_name' => 'Open', 'text_color' => '#FFFFFF', 'background_color' => '#03CFCF'],
            ['status_name' => 'In-Progress', 'text_color' => '#FFFFFF', 'background_color' => '#FF7601'],
            ['status_name' => 'Work Done', 'text_color' => '#FFFFFF', 'background_color' => '#12CB36'],
            ['status_name' => 'Closed', 'text_color' => '#595A63', 'background_color' => '#D5D5D6'],
        ];
        $clientTicketStatusType = [
            ['status_name' => 'Open', 'text_color' => '#FFFFFF', 'background_color' => '#000000'],
            ['status_name' => 'Done', 'text_color' => '#FFFFFF', 'background_color' => '#000000'],
            ['status_name' => 'Close', 'text_color' => '#FFFFFF', 'background_color' => '#000000'],
        ];

        $getSeederType = config('constant.SEEDER_TYPE');
        $ticketStatusTypes = ($getSeederType == 'owner') ? $ticketStatusType : $clientTicketStatusType;

        foreach ($ticketStatusTypes as $type){
            $chekExists = TicketStatus::where('status_name',$type)->exists();
            if(!$chekExists){
                $ticketStatus['status_name'] = $type['status_name'];
                $ticketStatus['text_color'] = $type['text_color'];
                $ticketStatus['background_color'] = $type['background_color'];
                $ticketStatus['is_lock'] = 1;
                TicketStatus::create($ticketStatus);
            }
        }
    }
}
