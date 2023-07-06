<?php

namespace App\Repositories\Dashboard;

use App\Models\Tickets;
use App\Models\TicketStatus;

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
        //dd(Tickets::query()->select('ticket_status_id')->groupBy('ticket_status_id')->selectRaw('count(*) as ticket_status_count')->get()->pluck('ticket_status_count','ticket_status_id')->toArray());
//        dd(TicketStatus::select('id','status_name')->withCount('tickets')->get()->pluck('tickets_count','status_name')->toArray());
        $data = [
          'total_ticket' => Tickets::count(),
          'open_ticket' => Tickets::where('ticket_status_id',1)->count(),
          'open_ticket_percentage' => Tickets::where('ticket_status_id',1)->count() / Tickets::count() * 100,
          'inprogress_ticket' => Tickets::where('ticket_status_id',2)->count(),
          'inprogress_ticket_percentage' => Tickets::where('ticket_status_id',2)->count() / Tickets::count() * 100,
          'workdone_ticket' => Tickets::where('ticket_status_id',3)->count(),
          'workdone_ticket_percentage' => Tickets::where('ticket_status_id',3)->count() / Tickets::count() * 100,
          'closed_ticket' => Tickets::where('ticket_status_id',4)->count(),
          'closed_ticket_percentage' => Tickets::where('ticket_status_id',4)->count() / Tickets::count() * 100,
        ];
        return $data;
    }
}
