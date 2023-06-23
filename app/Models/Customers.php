<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pricecurrent\LaravelEloquentFilters\Filterable;

class Customers extends Model
{
    use HasFactory,Filterable;
    protected $fillable = ['id','first_name','last_name','email','phone','created_at','updated_at'];

    public function locations()
    {
        return $this->hasMany(CustomerLocations::class,'customer_id','id');
    }

    public function phones()
    {
        return $this->hasMany(CustomerPhones::class,'customer_id','id');
    }
    public function contract()
    {
        return $this->belongsToMany(Contract::class,customerContract::class, 'customer_id','contract_id');
    }
}
