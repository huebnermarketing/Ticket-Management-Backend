<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable= ['contract_id','total_amount','paid_amount','outstanding_amount',
        'status','is_invoice_paid','deleted_at','created_at','updated_at'];
}
