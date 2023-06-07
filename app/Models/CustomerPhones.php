<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPhones extends Model
{
    use HasFactory;
    protected $fillable = ['id','customer_id','phone','is_primary','created_at','updated_at'];
}
