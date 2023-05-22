<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLocations extends Model
{
    use HasFactory;
    protected $fillable = ['id','customer_id','address_line1','area','zipcode','city','state','country',
        'is_primary','created_at','updated_at'];
}
