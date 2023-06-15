<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketComments extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['id','user_id','ticket_id','comment','comment_date','created_at','updated_at','deleted_at'];
}
