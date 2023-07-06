<?php

namespace App\Repositories\Dashboard;

use App\Models\Tickets;

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
          'open_ticket' => Tickets::where('ticket_status_id',1)->count(),
          'inprogress_ticket' => Tickets::where('ticket_status_id',1)->count(),
          'workdone_ticket' => Tickets::where('ticket_status_id',1)->count(),
          'closed_ticket' => Tickets::where('ticket_status_id',1)->count(),
          'total_ticket' => Tickets::count()
        ];
        return $data;
    }
}
