<?php

namespace App\Filters;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;
use Illuminate\Database\Eloquent\Builder;

class TicketFilter extends AbstractEloquentFilter
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder): Builder
    {
        $ticketQuery = $builder->with(['customer.phones'=> function($qry){ $qry->select('id','customer_id','phone','is_primary'); },
                'customer_location',
                'assigned_engineer'=> function($qry){ $qry->select('id','first_name','last_name','profile_photo'); },
                'appointment_type' => function($qry){ $qry->select('id','appointment_name'); },
                'ticket_priority' => function($qry){ $qry->select('id','priority_name','text_color','background_color'); },
                'ticket_status' => function($qry){ $qry->select('id','status_name','text_color','background_color'); },
                'payment_status' => function($qry){ $qry->select('id','payment_type','text_color','background_color'); }]);

        if(isset($this->request->customer_id) && (count($this->request->customer_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('customer_id',$this->request->customer_id);
        }
        if(isset($this->request->problem_type_id) && (count($this->request->problem_type_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('problem_type_id',$this->request->problem_type_id);
        }
        if(isset($this->request->ticket_status_id) && (count($this->request->ticket_status_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('ticket_status_id',$this->request->ticket_status_id);
        }
        if(isset($this->request->appointment_type_id) && (count($this->request->appointment_type_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('appointment_type_id',$this->request->appointment_type_id);
        }
        if(isset($this->request->payment_type_id) && (count($this->request->payment_type_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('payment_type_id',$this->request->payment_type_id);
        }
        if(isset($this->request->priority_id) && (count($this->request->priority_id) > 0)){
            $ticketQuery = $ticketQuery->whereIn('priority_id',$this->request->priority_id);
        }
        return $ticketQuery;
    }
}
