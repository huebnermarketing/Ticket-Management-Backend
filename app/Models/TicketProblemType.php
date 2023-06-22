<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketProblemType extends Model
{
    use HasFactory;
    protected $fillable = ['id','ticket_id','problem_type_id','created_at','updated_at'];
}
