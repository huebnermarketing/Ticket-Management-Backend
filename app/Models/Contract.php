<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pricecurrent\LaravelEloquentFilters\Filterable;
class Contract extends Model
{
    use HasFactory,Filterable;

    protected $fillable = ['unique_id','customer_id','customer_location_id','contract_title','contract_details','amount','duration_id',
        'payment_term_id','start_date','end_date','is_auto_renew','is_active','is_archive','is_suspended','remaining_amount'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->unique_id = static::generateId();
        });
    }
    protected static function generateId()
    {
        $lastRecord = static::query()->orderByDesc('id')->first();

        if ($lastRecord) {
            $newId = $lastRecord->unique_id + 1;
        } else {
            $newId = config('constant.CONTRACT_UNIQUE_ID');
        }
        return str_pad($newId, 5, '0', STR_PAD_LEFT);
    }
    public function customers()
    {
        return $this->hasMany(Customers::class, 'id','customer_id');
    }

    public function customerLocation()
    {
        return $this->belongsTo(CustomerLocations::class,'customer_id','customer_id');
    }

    public function contractServicesTypes()
    {
        return $this->hasMany(ContractServiceType::class,'contract_id','id');
    }
}
