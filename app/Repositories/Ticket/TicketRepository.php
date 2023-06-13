<?php

namespace App\Repositories\Ticket;

use App\Models\Tickets;
use \App\Repositories\Ticket\TicketRepositoryInterface;
use Carbon\Carbon;

class TicketRepository implements TicketRepositoryInterface
{
    public function getTickets($filters = null)
    {
        $sortValue = (!empty($filters) && array_key_exists('sort_value',$filters) && !empty($filters['sort_value'])) ? $filters['sort_value'] : 'id';
        $orderBy = (!empty($filters) && array_key_exists('order_by',$filters)) && !empty($filters['order_by']) ? $filters['order_by'] : 'DESC';
        $pageLimit = (!empty($filters) && array_key_exists('total_record',$filters)) && !empty($filters['total_record']) ? $filters['total_record'] : config('constant.PAGINATION_RECORD');

        return Tickets::with(['customer.phones'=> function($qry){ $qry->select('id','customer_id','phone'); },
            'customer_location'=> function($qry){ $qry->select('id','company_name');},
            'assigned_engineer'=> function($qry){ $qry->select('id','first_name','last_name','profile_photo'); },
            'appointment_type' => function($qry){ $qry->select('id','appointment_name'); },
            'ticket_priority' => function($qry){ $qry->select('id','priority_name'); },
            'ticket_status' => function($qry){ $qry->select('id','status_name'); },
            'payment_status' => function($qry){ $qry->select('id','payment_type'); }])
            ->orderBy($sortValue,$orderBy)->paginate($pageLimit);
    }

    public function ticketListDashboard(){
        $carbonDateNow = Carbon::now();
        $todayDate = Carbon::now()->toDateString();
        $startDate = $carbonDateNow->startOfWeek()->toDateString();
        $endDate = $carbonDateNow->endOfWeek()->toDateString();

        $ticketData['unresolved'] = Tickets::unresolvedTicket()->count();
        $ticketData['overdue'] = Tickets::where('due_date', '<', $todayDate)->count();
        $ticketData['due_today'] = Tickets::where('due_date',$todayDate)->count();
        $ticketData['due_this_week'] =Tickets::whereBetween('due_date',[$startDate,$endDate])->count();
        $ticketData['partially_paid'] = Tickets::paymentStatus('Partially Paid')->count();
        $ticketData['unpaid'] = Tickets::paymentStatus('Unpaid')->count();
        return $ticketData;
    }

    public function storeTicket($data)
    {
        $ticketPayload['ticket_type'] = $data['ticket_type'];
        $ticketPayload['url_slug'] = $data['url_slug'];
        $ticketPayload['customer_id'] = $data['customer_id'];
        $ticketPayload['customer_locations_id'] = $data['customer_locations_id'];

        $ticketPayload['problem_type_id'] = $data['problem_type_id'];
        $ticketPayload['problem_title'] = $data['problem_title'];
        $ticketPayload['due_date'] = $data['due_date'];
        $ticketPayload['description'] = $data['description'];
        $ticketPayload['ticket_status_id'] = $data['ticket_status_id'];
        $ticketPayload['priority_id'] = $data['priority_id'];
        $ticketPayload['assigned_user_id'] = $data['assigned_user_id'];
        $ticketPayload['appointment_type_id'] = $data['appointment_type_id'];

        $ticketPayload['amount'] = $data['ticket_amount'];
        $ticketPayload['payment_type_id'] = $data['payment_type_id'];
        $ticketPayload['collected_amount'] = $data['collected_amount'];
        $ticketPayload['remaining_amount'] = $data['remaining_amount'];
        $ticketPayload['payment_mode'] = $data['payment_mode'];
        return Tickets::create($ticketPayload);
    }
}
