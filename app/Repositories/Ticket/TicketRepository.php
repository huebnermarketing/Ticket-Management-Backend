<?php

namespace App\Repositories\Ticket;

use App\Models\Tickets;
use \App\Repositories\Ticket\TicketRepositoryInterface;
use Carbon\Carbon;

class TicketRepository implements TicketRepositoryInterface
{
    public function getTickets($filters = null, $request = null)
    {
        $sortValue = (!empty($filters) && array_key_exists('sort_value',$filters) && !empty($filters['sort_value'])) ? $filters['sort_value'] : 'id';
        $orderBy = (!empty($filters) && array_key_exists('order_by',$filters)) && !empty($filters['order_by']) ? $filters['order_by'] : 'DESC';
        $pageLimit = (!empty($filters) && array_key_exists('total_record',$filters)) && !empty($filters['total_record']) ? $filters['total_record'] : config('constant.PAGINATION_RECORD');

        $ticketQuery = Tickets::with(['customer.phones'=> function($qry){ $qry->select('id','customer_id','phone'); },
                'customer_location',
                'assigned_engineer'=> function($qry){ $qry->select('id','first_name','last_name','profile_photo'); },
                'appointment_type' => function($qry){ $qry->select('id','appointment_name'); },
                'ticket_priority' => function($qry){ $qry->select('id','priority_name'); },
                'ticket_status' => function($qry){ $qry->select('id','status_name'); },
                'payment_status' => function($qry){ $qry->select('id','payment_type'); }]);

        if(isset($request->customer_id) && (count($request->customer_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('customer_id',$request->customer_id);
        }
        if(isset($request->problem_type_id) && (count($request->problem_type_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('problem_type_id',$request->problem_type_id);
        }
        if(isset($request->ticket_status_id) && (count($request->ticket_status_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('ticket_status_id',$request->ticket_status_id);
        }
        if(isset($request->appointment_type_id) && (count($request->appointment_type_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('appointment_type_id',$request->appointment_type_id);
        }
        if(isset($request->payment_type_id) && (count($request->payment_type_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('payment_type_id',$request->payment_type_id);
        }
        if(isset($request->priority_id) && (count($request->priority_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('priority_id',$request->priority_id);
        }
        $ticketQuery = $ticketQuery->orderBy($sortValue,$orderBy)->paginate($pageLimit);

        return $ticketQuery;
//        return Tickets::ticketRelations()->orderBy($sortValue,$orderBy)->paginate($pageLimit);
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
        $ticketPayload = [
            'ticket_type' => $data['ticket_type'],
            'url_slug' => $data['url_slug'],
            'customer_id' => $data['customer_id'],
            'customer_locations_id' => $data['customer_locations_id'],

            'problem_type_id' => $data['problem_type_id'],
            'problem_title' => $data['problem_title'],
            'due_date' => $data['due_date'],
            'description' => $data['description'],
            'ticket_status_id' => $data['ticket_status_id'],
            'priority_id' => $data['priority_id'],
            'assigned_user_id' => $data['assigned_user_id'],
            'appointment_type_id' => $data['appointment_type_id'],

            'amount' => $data['ticket_amount'],
            'payment_type_id' => $data['payment_type_id'],
            'collected_amount' => $data['collected_amount'],
            'remaining_amount' => $data['remaining_amount'],
            'payment_mode' => $data['payment_mode']
        ];
        return Tickets::create($ticketPayload);
    }

    public function findTicket($id)
    {
        return Tickets::find($id);
    }

    public function updateTicket($data,$ticketId)
    {
        $updateTicket = [
            'ticket_type' => $data['ticket_type'],
            'customer_locations_id' => $data['customer_locations_id'],

            'problem_type_id' => $data['problem_type_id'],
            'problem_title' => $data['problem_title'],
            'due_date' => $data['due_date'],
            'description' => $data['description'],
            'ticket_status_id' => $data['ticket_status_id'],
            'priority_id' => $data['priority_id'],
            'assigned_user_id' => $data['assigned_user_id'],
            'appointment_type_id' => $data['appointment_type_id'],

            'amount' => $data['ticket_amount'],
            'payment_type_id' => $data['payment_type_id'],
            'collected_amount' => $data['collected_amount'],
            'remaining_amount' => $data['remaining_amount'],
            'payment_mode' => $data['payment_mode']
        ];
        return Tickets::where('id',$ticketId)->update($updateTicket);
    }
}
