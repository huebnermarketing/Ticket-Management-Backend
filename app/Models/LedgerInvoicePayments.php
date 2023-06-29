<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerInvoicePayments extends Model
{
    use HasFactory;
    protected $fillable= ['invoice_id','ledger_invoice_id','contract_id','adjustable_amount','created_at','updated_at'];
}
