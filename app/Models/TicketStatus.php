<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketStatus extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['id','status_name','is_lock','deleted_at','created_at','updated_at'];
}
