<?php

namespace App\Models;

use App\Traits\CommonTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketStatus extends Model
{
    use HasFactory,SoftDeletes,CommonTrait;
    protected $fillable = ['id','unique_id','status_name','text_color','background_color','is_lock','deleted_at','created_at','updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->unique_id = static::generateId();
        });
    }

    public function Tickets()
    {
        return $this->hasMany(Tickets::class);
    }
}
