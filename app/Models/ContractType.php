<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractType extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['id','contract_name','deleted_at','created_at','updated_at'];

}
