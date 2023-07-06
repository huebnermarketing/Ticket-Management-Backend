<?php

namespace App\Repositories\Dashboard;

use App\Models\Tickets;
use App\Models\TicketStatus;
use DB;

class DashboardRepository implements DashboardRepositoryInterface{
    public function getDetails(){
        $data = [
            'open_ticket_count' => Tickets::whereIn('ticket_status_id',[1,2,3])->count(),
            'overdue_ticket_count' => Tickets::where('due_date','<',date('Y-m-d'))->count(),
            'today_overdue_ticket_count' => Tickets::where('due_date','=',date('Y-m-d'))->count(),
            'sum_open_ticket' => Tickets::whereNot('ticket_status_id',4)->sum('remaining_amount')
        ];
        return $data;
    }

    public function getTicketStatus(){
        $data = [
            'total_ticket_count' => Tickets::count(),
            'ticket_status_by_percentage' => TicketStatus::select('status_name as status')->withCount('tickets as count')
                ->get()
                ->map(function ($item) {
                    $item->percentage = ($item->count / Tickets::count() * 100);
                    return $item;
                })
                ->toArray()
        ];
        return $data;
//        $ticketCount = Tickets::count();
//        $ticketStatusByPercentage = TicketStatus::select('status_name as status')->withCount('tickets as count')->get();
//        $ticketStatusByPercentage->map(function ($item) use($ticketCount) {
//            $item->percentage = ($item->count / $ticketCount * 100);
//            return $item;
//        });
//        $data['ticket_status_by_percentage'] = $ticketStatusByPercentage->toArray();
//        $data['total_ticket_count'] = $ticketCount;
//        return $data;
    }
}
