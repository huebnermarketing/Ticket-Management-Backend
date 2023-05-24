<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordVerificationEmail extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','token','created_at','updated_at'];
}
