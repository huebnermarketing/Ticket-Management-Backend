<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketComments extends Model
{
    use HasFactory;
    protected $fillable = ['id','user_id','ticket_id','comment','comment_date','created_at','updated_at','deleted_at'];
}
