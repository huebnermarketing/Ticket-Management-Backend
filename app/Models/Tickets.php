<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TicketTypesEnum;
use Carbon\Carbon;

class Tickets extends Model
{
    use HasFactory;
//    protected $casts = ['ticket_type' => TicketTypesEnum::class];
    protected $fillable = ['id','url_slug','ticket_type','customer_id','customer_locations_id','problem_type_id',
        'ticket_status_id','priority_id','assigned_user_id','appointment_type_id','payment_type_id','problem_title','due_date',
        'description','amount','collected_amount','remaining_amount','payment_mode','created_at','updated_at','deleted_at'];

//    public static function getTicketTypeValues()
//    {
//        return TicketTypesEnum::values();
//    }

    public function customer()
    {
        return $this->belongsTo(Customers::class,'customer_id','id');
    }

    public function customer_location()
    {
        return $this->belongsTo(CustomerLocations::class,'customer_locations_id','id');
    }

    public function assigned_engineer()
    {
        return $this->belongsTo(User::class,'assigned_user_id','id');
    }

    public function appointment_type()
    {
        return $this->belongsTo(AppointmentTypes::class,'appointment_type_id','id');
    }

    public function ticket_priority()
    {
        return $this->belongsTo(TicketPriority::class,'priority_id','id');
    }

    public function ticket_status()
    {
        return $this->belongsTo(TicketStatus::class,'ticket_status_id','id');
    }

    public function payment_status()
    {
        return $this->belongsTo(PaymentTypes::class,'payment_type_id','id');
    }

    public function scopePaymentStatus($query,$status)
    {
        return $query->with(['payment_status'])->whereHas('payment_status', function ($qry) use ($status){
            $qry->where('payment_type',$status);
        });
    }

    public function scopeUnresolvedTicket($query)
    {
        return $query->with(['ticket_status'])->whereHas('ticket_status', function ($qry){
            $qry->whereNotIn('status_name',['Closed']);
        });
    }
}
