<?php

namespace App\Models;

use App\Traits\CommonTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pricecurrent\LaravelEloquentFilters\Filterable;
class Contract extends Model
{
    use HasFactory,Filterable,CommonTrait;

    protected $fillable = ['unique_id','customer_id','customer_location_id','contract_title','contract_details','amount','duration_id',
        'payment_term_id','start_date','end_date','is_auto_renew','is_active','is_archive','is_suspended','remaining_amount'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->unique_id = static::generateId('contract');
        });
    }

    public function customers()
    {
        return $this->belongsTo(Customers::class,'customer_id','id');
    }

    public function customerLocation()
    {
        return $this->belongsTo(CustomerLocations::class,'customer_location_id','id');
    }

    public function contractServicesTypes()
    {
       return $this->hasMany(ContractServiceType::class,'contract_id','id');
    }

    public function manyServiceType()
    {
        return $this->belongsToMany(ContractType::class, ContractServiceType::class, 'contract_id', 'contract_type_id');
    }

    public function tickets()
    {
        return $this->hasMany(Tickets::class, 'contract_id','id');
    }

    public function contractProductServices(){
        return $this->belongsToMany(ProductServices::class, ContractProductService::class, 'contract_id', 'product_service_id');
    }

    public function productService(){
        return $this->hasMany(ContractProductService::class,'contract_id','id');
    }

    public function duration(){
        return $this->belongsTo(ContractDuration::class,'duration_id','id');
    }

    public function paymentTerm(){
        return $this->belongsTo(ContractPaymentTerm::class,'payment_term_id','id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoices::class,'contract_id','id');
    }
}
