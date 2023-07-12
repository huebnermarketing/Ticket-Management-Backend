<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractProductService extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id','product_service_id','product_qty','product_amount'];

    public function productService(){
        return $this->belongsTo(ProductServices::class,'product_service_id','id');
    }
//    protected static function boot()
//    {
//        parent::boot();
//        static::creating(function ($model) {
//            $model->unique_id = static::generateId();
//        });
//    }
}
