<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id','customer_location_id','contract_title','contract_details','amount','duration_id',
        'payment_term_id','start_date','end_date','is_auto_renew','is_active','is_archive','is_suspended'];
}
