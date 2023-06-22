<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractProductService extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id','product_service_id','product_qty','product_amount'];
}
