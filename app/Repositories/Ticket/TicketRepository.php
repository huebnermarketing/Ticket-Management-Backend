<?php

namespace App\Repositories\Ticket;

use App\Models\TicketProblemType;
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

        return Tickets::ticketRelations()->orderBy($sortValue,$orderBy)->paginate($pageLimit);
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
            'unique_id' => $data['unique_id'],
            'customer_id' => $data['customer_id'],
            'customer_locations_id' => $data['customer_locations_id'],

            //'problem_type_id' => $data['problem_type_id'],
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
        $createTicket = Tickets::create($ticketPayload);

        foreach ($data['problem_type_id'] as $problems){
            $ticketProblemTypePayload = [
                'ticket_id' => $createTicket['id'],
                'problem_type_id' => $problems
            ];
            $ticketProblemType = TicketProblemType::create($ticketProblemTypePayload);
        }
        return $createTicket;
    }

    public function findTicket($id)
    {
        return Tickets::find($id);
    }

    public function updateTicket($data,$ticketId)
    {
        $getTicket = $this->findTicket($ticketId);
        $getTicket['ticket_type'] = $data['ticket_type'];
        $getTicket['customer_locations_id'] = $data['customer_locations_id'];
        $getTicket['problem_title'] = $data['problem_title'];
        $getTicket['due_date'] = $data['due_date'];
        $getTicket['description'] = $data['description'];
        $getTicket['ticket_status_id'] = $data['ticket_status_id'];
        $getTicket['priority_id'] = $data['priority_id'];
        $getTicket['assigned_user_id'] = $data['assigned_user_id'];
        $getTicket['appointment_type_id'] = $data['appointment_type_id'];

        $getTicket['amount'] = $data['ticket_amount'];
        $getTicket['payment_type_id'] = $data['payment_type_id'];
        $getTicket['collected_amount'] = $data['collected_amount'];
        $getTicket['remaining_amount'] = $data['remaining_amount'];
        $getTicket['payment_mode'] = $data['payment_mode'];
        $updateTicket = $getTicket->save();
        $getTicket->problem_types()->sync($data['problem_type_id']);
        return $updateTicket;
    }
}
