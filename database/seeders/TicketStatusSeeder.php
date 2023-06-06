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
        $ticketStatusType = ['Open','In-Progress','Work Done','Closed','Non-Billable','Uncollectible'];
        $clientTicketStatusType = ['Open','Done','Close'];

        $getSeederType = config('constant.SEEDER_TYPE');
        $ticketStatusTypes = ($getSeederType == 'owner') ? $ticketStatusType : $clientTicketStatusType;

        foreach ($ticketStatusTypes as $type){
            $chekExists = TicketStatus::where('status_name',$type)->exists();
            if(!$chekExists){
                $ticketStatus['status_name'] = $type;
                $ticketStatus['is_lock'] = 1;
                TicketStatus::create($ticketStatus);
            }
        }
    }
}
