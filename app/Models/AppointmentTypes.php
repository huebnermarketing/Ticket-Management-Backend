<?php

namespace App\Models;

use App\Traits\CommonTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentTypes extends Model
{
    use HasFactory,SoftDeletes,CommonTrait;

    protected $fillable = ['id','unique_id','appointment_name','deleted_at','created_at','updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->unique_id = static::generateId();
        });
    }
    /*protected static function generateId()
    {
        $lastRecord = static::query()->withTrashed()->orderByDesc('id')->first();
        if ($lastRecord) {
            $newId = $lastRecord->unique_id + 1;
        } else {
            $newId = config('constant.STATUSES_UNIQUE_ID');
        }
        return str_pad($newId, 5, '0', STR_PAD_LEFT);
    }*/
}
