<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractServiceType extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id','contract_type_id'];

    public function contractTypes(){
        return $this->belongsTo(ContractType::class,'contract_type_id','id');
    }
}
