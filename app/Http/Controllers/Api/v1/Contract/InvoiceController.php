<?php

namespace App\Http\Controllers\Api\v1\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoices;
use App\Models\LedgerInvoicePayment;
use App\Models\LedgerInvoices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RestResponse;
use Validator;

class InvoiceController extends Controller
{

    public function createInvoices($contractId)
    {
        try{
            $contractId = 4;
            $getContract = Contract::with(['duration','payment_term'])->find($contractId);
            if(empty($getContract)){
                return RestResponse::warning('Contract not found.');
            }
            $getDuration = $this->getContractDuration($getContract['duration']['slug']);
            $getPaymentTerm = $this->getPaymentTerm($getContract['payment_term']['slug'],$getDuration);

            $totalInvoices = $getDuration / $getPaymentTerm;
            $perInvoiceAmount = $getContract['amount'] / $totalInvoices;

            for ($i = 0; $i < $totalInvoices; $i++) {
                $invoices = [
                    'contract_id' => $getContract['id'],
                    'total_amount' => $perInvoiceAmount,
                    'outstanding_amount' => $perInvoiceAmount,
                    'status' => 'Unpaid',
                ];
                $createInvoice = Invoices::create($invoices);
            }
            return RestResponse::Success('Contract invoices created successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function getContractDuration($duration){
        if($duration == 'year'){
            return 12;
        }else if($duration == 'half-year'){
            return 6;
        }else if($duration == 'qtr'){
            return 3;
        }else if($duration == 'month'){
            return 1;
        }
    }

    public function getPaymentTerm($paymentTerm,$duration){
        if($paymentTerm == 'month'){
            return 1;
        }else if($paymentTerm == 'qtr'){
            return 3;
        }else if($paymentTerm == 'half-year'){
            return 6;
        }else if($paymentTerm == 'all-at-once'){
            return $duration;
        }
    }

    public function getInvoiceDetails($contractId)
    {
        try{
            $getContract = Contract::with(['customers','customerLocation','invoices'])->find($contractId);
            if(empty($getContract)){
                return RestResponse::warning('Contract not found.');
            }
            $invoice['contract_amount'] = $getContract['amount'];
            $invoice['paid_amount'] = $getContract['amount'] - $getContract['remaining_amount'];
            $invoice['outstanding_amount'] = $getContract['remaining_amount'];
            $invoice['ledger'] = [
                'first_name' => $getContract['customers']['first_name'],
                'last_name' => $getContract['customers']['last_name'],
                'company_name' => $getContract['customerLocation']['company_name'],
                'area' => $getContract['customerLocation']['area'],
                'city' => $getContract['customerLocation']['city'],
            ];
            $invoice['invoices'] = $getContract['invoices'];
            return RestResponse::Success($invoice, 'Contract retrieve successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function payInvoiceAmount(Request $request)
    {
        try{
            $validate = Validator::make($request->all(), [
                'contract_id' => 'required',
                'pay_amount' => 'required',
                'date' => 'required',
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }

            $getContract = Contract::with(['customers:id,first_name,last_name','customerLocation:id,customer_id,company_name'])
                ->select('id','customer_id','customer_location_id','contract_title')->find($request['contract_id']);
            if(empty($getContract)){
                return RestResponse::warning('Contract not found.');
            }

            if($request['pay_amount'] < $getContract['remaining_amount']){
                return RestResponse::warning("Payment amount can't be greater than outstanding amount.");
            }

           /* $name = Str::substr($getContract['customers']['first_name'], 0, 1);
            if(!empty($getContract['customers']['last_name'])){
                $name = $name. Str::substr($getContract['customers']['last_name'], 0, 1);
            }
            if(!empty($getContract['customerLocation']['company_name'])){
                $name = $name . Str::substr($getContract['customerLocation']['company_name'], 0, 1);
            }
            $currentTimestamp = Carbon::now()->timestamp;
            $invoiceUniqueId = $name.'-'.$currentTimestamp.'-'.$getUnpaidInvoice['unique_id'];*/

            $invoiceUniqueId = LedgerInvoices::ledgerUniqueId($getContract['customers']['first_name'],$getContract['customers']['last_name'],$getContract['customerLocation']['company_name']);
            $ledgerPayload = [
               'ledger_unique_id' => $invoiceUniqueId,
               'contract_id' => $request['contract_id'],
               'date' => $request['date'],
               'ledger_amount' => $request['pay_amount'],
            ];
            $createLedgerInvoice = LedgerInvoices::create($ledgerPayload);


            $getUnpaidInvoice = Invoices::where(['contract_id' => $request['contract_id'],'is_invoice_paid' => 0])->first();
            if(empty($getUnpaidInvoice)){
                return RestResponse::warning('Contract invoice not found.');
            }

            if($getUnpaidInvoice['outstanding_amount'] >= $request['pay_amount']){
                $payload['paid_amount'] = $request['pay_amount'];
                $payload['outstanding_amount'] = $getUnpaidInvoice['outstanding_amount'] - $request['pay_amount'];
                $payload['status'] = 'Partially Paid';
            }else {
                dd('elsee');
            }
            $getUnpaidInvoice->save();

            $ledgerPivot = [
                'invoice_id' => $getUnpaidInvoice['id'],
                'ledger_invoice_id' => $createLedgerInvoice['id'],
                'contract_id' => $request['contract_id'],
                'adjustable_amount' =>''
            ];
            $createLedgerInvoicePayment = LedgerInvoicePayment::create($ledgerPivot);
            return RestResponse::Success('Contract invoice amount added successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
