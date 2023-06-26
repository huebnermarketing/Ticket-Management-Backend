<?php

namespace App\Models;

use App\Traits\CommonTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTypes extends Model
{
    use HasFactory,SoftDeletes,CommonTrait;
    protected $fillable = ['id','unique_id','payment_type','is_active','deleted_at','created_at','updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->unique_id = static::generateId();
        });
    }
}
