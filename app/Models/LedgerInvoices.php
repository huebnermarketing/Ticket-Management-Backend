<?php

namespace App\Models;

use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LedgerInvoices extends Model
{
    use HasFactory,CommonTrait,SoftDeletes;
    protected $fillable= ['ledger_unique_id','contract_id','date','ledger_amount','deleted_at','created_at','updated_at'];

    public static function ledgerUniqueId($firstName,$lastName,$companyName){
        $name = Str::substr($firstName, 0, 1);
        if(!empty($lastName)){
            $name = $name. Str::substr($lastName, 0, 1);
        }
        if(!empty($companyName)){
            $name = $name . Str::substr($companyName, 0, 1);
        }
        $currentTimestamp = Carbon::now()->timestamp;
        $invoiceUniqueId = $name.'-'.$currentTimestamp.'-'.static::generateId('invoice');
        return $invoiceUniqueId;
    }
}
