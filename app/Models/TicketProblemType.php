<?php

namespace App\Models;

use App\Traits\CommonTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketProblemType extends Model
{
    use HasFactory,CommonTrait;
    protected $fillable = ['id','unique_id','ticket_id','problem_type_id','created_at','updated_at'];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->unique_id = static::generateId();
        });
    }
}
