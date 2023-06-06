<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentTypes extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['id','appointment_name','is_active','created_at','updated_at'];
}
