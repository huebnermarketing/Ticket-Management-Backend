<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TicketTypesEnum;

class Tickets extends Model
{
    use HasFactory;
    protected $casts = ['ticket_type' => TicketTypesEnum::class];
    protected $fillable = ['id','ticket_type','customer_id','customer_locations_id','problem_type_id',
        'ticket_status_id','assigned_user_id','appointment_type_id','payment_type_id','problem_title','due_date',
        'description','amount','collected_amount','remaining_amount','payment_mode','created_at','updated_at','deleted_at'];

//    public static function getTicketTypeValues()
//    {
//        return TicketTypesEnum::values();
//    }
}
