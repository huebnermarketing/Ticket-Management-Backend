<?php

namespace App\Repositories\Dashboard;

use App\Models\Contract;
use App\Models\Invoices;
use App\Models\PaymentTypes;
use App\Models\ProblemType;
use App\Models\TicketPriority;
use App\Models\Tickets;
use App\Models\TicketStatus;
use App\Models\User;
use Carbon\Carbon;
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
    }

    public function getTicketProblem(){
        return ProblemType::select('problem_name')->withCount('ticketsProblem as problem_ticket_count')->orderBy('problem_ticket_count', 'desc')->get();
    }

    public function getTicketPayment(){
        $data = [
            'total_ticket_count' => Tickets::count(),
            'ticket_problem_by_percentage' => PaymentTypes::select('payment_type')->withCount('tickets as count')
                ->get()
                ->map(function ($item) {
                    $item->percentage = ($item->count / Tickets::count() * 100);
                    return $item;
                })
                ->toArray()
        ];
        return $data;
    }

    public function getTicketPriority(){
        $data = [
            'total_ticket_count' => Tickets::count(),
            'ticket_problem_by_percentage' => TicketPriority::select('priority_name')->withCount('tickets as count')
                ->get()
                ->map(function ($item) {
                    $item->percentage = ($item->count / Tickets::count() * 100);
                    return $item;
                })
                ->toArray()
        ];
        return $data;
    }

    public function getTicketAssignee(){
        return User::select('first_name','last_name')->withCount('tickets as ticket_assignee_count')->orderBy('ticket_assignee_count', 'desc')->get();;
    }

    public function getTicketRevenue(){
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $month['month'] = date('F', strtotime("-$i months"));
            $month['year'] = date('Y', strtotime("-$i months"));

            $month['startDate'] = $startMonth = Carbon::now()->addMonth(-$i)->day(1)->format("Y-m-d");
            $month['endDate'] = $endMonth = Carbon::now()->addMonth(-$i)->endOfMonth()->format("Y-m-d");
            $invoiceRevenueTotal = Invoices::whereBetween('created_at',[$startMonth,$endMonth])->sum('paid_amount');
            $ticketRevenueTotal = Tickets::whereBetween('created_at',[$startMonth,$endMonth])->sum('collected_amount');
            $month['count'] = ($invoiceRevenueTotal + $ticketRevenueTotal);
            $months[] = $month;
        }
        return $months;
    }
}
