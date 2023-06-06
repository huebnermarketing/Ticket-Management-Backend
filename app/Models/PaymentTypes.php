<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTypes extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['id','payment_type','is_active','created_at','updated_at'];
}
