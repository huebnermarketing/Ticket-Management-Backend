<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdhocTicketAmount extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id','amount','payment_mode'];
}
