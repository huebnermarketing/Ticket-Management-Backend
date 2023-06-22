<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customerContract extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id','customer_id'];
}
