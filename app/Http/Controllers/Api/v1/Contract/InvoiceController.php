<?php

namespace App\Http\Controllers\Api\v1\Contract;

use App\Http\Controllers\Controller;
use App\Models\AdhocTicketAmount;
use App\Models\Contract;
use App\Models\Invoices;
use App\Models\LedgerInvoicePayments;
use App\Models\LedgerInvoices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RestResponse;
use Validator;

class InvoiceController extends Controller
{
    public function createInvoices($contractId)
    {
        try{
            $getContract = Contract::with(['duration','paymentTerm'])->find($contractId);
            if(empty($getContract)){
                return RestResponse::warning('Contract not found.');
            }
            $getDuration = $this->getContractDuration($getContract['duration']['slug']);
            $getPaymentTerm = $this->getPaymentTerm($getContract['paymentTerm']['slug'],$getDuration);

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
    public function updateInvoices($contractId,$updatedAmount,$invoicePaidAmount){
        try{
            $remainingInvoiceAmount = $updatedAmount - $invoicePaidAmount;
            $getUnpaidInvoices = Invoices::where(['contract_id' => $contractId, 'status'=>'Unpaid','is_invoice_paid' => 0])->get();
            if(count($getUnpaidInvoices) == 0) {
                //If no any unpaid invoice found then adjust all remaining amount in Partially Paid invoice.
                $partiallyPaidInvoices = Invoices::where(['contract_id' => $contractId, 'status'=>'Partially Paid'])->first();
                if(!empty($partiallyPaidInvoices)){
                    if($remainingInvoiceAmount == 0){
                        $partiallyPaidInvoices['total_amount'] = $partiallyPaidInvoices['paid_amount'];
                        $partiallyPaidInvoices['outstanding_amount'] = 0;
                        $partiallyPaidInvoices['status'] = 'Paid';
                        $partiallyPaidInvoices['is_invoice_paid'] = 1;
                        $partiallyPaidInvoices->save();
                    }else {
                        $partiallyPaidInvoices['total_amount'] = $remainingInvoiceAmount + $partiallyPaidInvoices['paid_amount'];
                        $partiallyPaidInvoices['outstanding_amount'] = $partiallyPaidInvoices['total_amount'] - $partiallyPaidInvoices['paid_amount'];
                        $partiallyPaidInvoices->save();                    }
                }
                $getRemainingInvoiceSum = Invoices::where(['contract_id' => $contractId,'is_invoice_paid' => 0])->whereIn('status',['Unpaid','Partially Paid'])->sum('outstanding_amount');
                $updateContract = Contract::where('id',$contractId)->update(['remaining_amount' => $getRemainingInvoiceSum]);
            }else{
                //First adjust remaining amount in Partially Paid invoice.
                $partiallyPaidInvoices = Invoices::where(['contract_id' => $contractId, 'status'=>'Partially Paid'])->first();
                if(!empty($partiallyPaidInvoices)){
                    if($remainingInvoiceAmount == 0){
                        $partiallyPaidInvoices['total_amount'] = $partiallyPaidInvoices['paid_amount'];
                    }else if($partiallyPaidInvoices['outstanding_amount'] <= $remainingInvoiceAmount){
                        $remainingInvoiceAmount -= $partiallyPaidInvoices['outstanding_amount'];
                        $partiallyPaidInvoices['paid_amount'] = $partiallyPaidInvoices['paid_amount'] + $partiallyPaidInvoices['outstanding_amount'];
                        $ledgerPaymentPivot = [
                            'invoice_id' => $partiallyPaidInvoices['id'],
                            'contract_id' => $contractId,
                            'adjustable_amount' => $partiallyPaidInvoices['outstanding_amount'],
                            'is_contract_update' => 1
                        ];
                        $createLedgerInvoicePayment = LedgerInvoicePayments::create($ledgerPaymentPivot);
                    }else{
                        $partiallyPaidInvoices['total_amount'] = $partiallyPaidInvoices['paid_amount'];
                    }
                    $partiallyPaidInvoices['outstanding_amount'] = 0;
                    $partiallyPaidInvoices['status'] = 'Paid';
                    $partiallyPaidInvoices['is_invoice_paid'] = 1;
                    $partiallyPaidInvoices->save();
                }
                //Adjust remaining amount in Unpaid invoice.
                if(count($getUnpaidInvoices) > 0){
                    if($remainingInvoiceAmount == 0){
                        $this->changeInvoiceStatus($contractId);
                    }else{
                        $perInvoiceAmount = (int) round($remainingInvoiceAmount / count($getUnpaidInvoices),0);
                        foreach ($getUnpaidInvoices as $invoices){
                            $invoices['total_amount'] = $perInvoiceAmount;
                            $invoices['outstanding_amount'] = $perInvoiceAmount;
                            $invoices->save();
                        }
                    }
                }
                $getRemainingInvoiceSum = Invoices::where(['contract_id' => $contractId,'is_invoice_paid' => 0 ,'status' => 'Unpaid'])->sum('outstanding_amount');
                $updateContract = Contract::where('id',$contractId)->update(['remaining_amount' => $getRemainingInvoiceSum]);
            }
            return true;
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function getContractDuration($duration){
        /*if($duration == 'year'){
            return 12;
        }else if($duration == 'half-year'){
            return 6;
        }else if($duration == 'qtr'){
            return 3;
        }else if($duration == 'month'){
            return 1;
        }*/
        $mapping = [
            'year' => 12,
            'half-year' => 6,
            'qtr' => 3,
            'month' => 1,
        ];
        return $mapping[$duration]?? null;
    }

    public function getPaymentTerm($paymentTerm,$duration){
        /*if($paymentTerm == 'month'){
            return 1;
        }else if($paymentTerm == 'qtr'){
            return 3;
        }else if($paymentTerm == 'half-year'){
            return 6;
        }else if($paymentTerm == 'all-at-once'){
            return $duration;
        }*/
        $paymentTermMap = [
            'month' => 1,
            'qtr' => 3,
            'half-year' => 6,
            'all-at-once' => $duration,
        ];

        return $paymentTermMap[$paymentTerm]?? null;
    }

    public function getInvoiceDetails($contractId)
    {
        try{
            $getContract = Contract::with(['customers','customerLocation','invoices','ledgerInvoice'])->find($contractId);
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
            $invoice['invoices'] = $getContract['ledgerInvoice'];
            $invoice['payment_mode'] = ['card', 'cash', 'online'];
            return RestResponse::Success($invoice, 'Contract retrieve successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function payInvoiceAmount(Request $request)
    {
        try{
            DB::beginTransaction();
            $validate = Validator::make($request->all(), [
                'contract_id' => 'required',
                'pay_amount' => 'required',
                'date' => 'required',
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }

            $getContract = Contract::with(['customers:id,first_name,last_name','customerLocation:id,customer_id,company_name'])
                ->select('id','customer_id','customer_location_id','contract_title','remaining_amount')->find($request['contract_id']);
            if(empty($getContract)){
                return RestResponse::warning('Contract not found.');
            }
            if($request['pay_amount'] > $getContract['remaining_amount']){
                return RestResponse::warning("Payment amount can't be greater than outstanding amount.");
            }

            $invoiceUniqueId = LedgerInvoices::ledgerUniqueId($getContract['customers']['first_name'],$getContract['customers']['last_name'],$getContract['customerLocation']['company_name']);
            $ledgerPayload = [
               'ledger_unique_id' => $invoiceUniqueId,
               'contract_id' => $request['contract_id'],
               'date' => $request['date'],
               'ledger_amount' => $request['pay_amount'],
               'payment_mode' => $request['payment_mode']
            ];
            $createLedgerInvoice = LedgerInvoices::create($ledgerPayload);
            $getContract['remaining_amount'] = $getContract['remaining_amount'] - $request['pay_amount'];
            $getContract->save();

            $adjustAmount = $this->adjustInvoiceAmount($request['contract_id'],$request['pay_amount'],$createLedgerInvoice['id']);
            DB::commit();
            return RestResponse::Success('Contract invoice amount added successfully.');
        }catch (\Exception $e) {
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function adjustInvoiceAmount($contractId,$requestPayAmount,$ledgerInvoiceId){
        $getUnpaidInvoice = Invoices::where(['contract_id' => $contractId,'is_invoice_paid' => 0])->whereNot('status','Uncollectible')->first();
       if(empty($getUnpaidInvoice)){
            return RestResponse::warning('Contract invoice not found.');
        }

        $requestedAmount = $requestPayAmount;
        if($requestedAmount == 0){
            return false;
        }else {
            if($requestedAmount > $getUnpaidInvoice['outstanding_amount']){
                $ledgerPivot = [
                    'invoice_id' => $getUnpaidInvoice['id'],
                    'ledger_invoice_id' => $ledgerInvoiceId,
                    'contract_id' => $contractId,
                    'adjustable_amount' => $getUnpaidInvoice['outstanding_amount']
                ];
                $createLedgerInvoicePayment = LedgerInvoicePayments::create($ledgerPivot);
                $getUnpaidInvoice['paid_amount'] = $getUnpaidInvoice['paid_amount'] + $getUnpaidInvoice['outstanding_amount'];
                $getUnpaidInvoice['outstanding_amount'] = 0;
                $getUnpaidInvoice['status'] = 'Paid';
                $getUnpaidInvoice['is_invoice_paid'] = 1;
                $requestedAmount = $requestPayAmount - $createLedgerInvoicePayment['adjustable_amount'];
                $getUnpaidInvoice->save();
                $this->adjustInvoiceAmount($contractId,$requestedAmount,$ledgerInvoiceId);
            }else{
                if($requestedAmount == $getUnpaidInvoice['outstanding_amount']){
                    $getUnpaidInvoice['status'] = 'Paid';
                    $getUnpaidInvoice['is_invoice_paid'] = 1;
                }else{
                    $getUnpaidInvoice['status'] = 'Partially Paid';
                }
                $getUnpaidInvoice['paid_amount'] = $getUnpaidInvoice['paid_amount'] + $requestPayAmount;
                $getUnpaidInvoice['outstanding_amount'] = $getUnpaidInvoice['outstanding_amount'] - $requestPayAmount;
                $getUnpaidInvoice->save();
                $requestedAmount = 0;
                $ledgerPivot = [
                    'invoice_id' => $getUnpaidInvoice['id'],
                    'ledger_invoice_id' => $ledgerInvoiceId,
                    'contract_id' => $contractId,
                    'adjustable_amount' => $requestPayAmount
                ];
                $createLedgerInvoicePayment = LedgerInvoicePayments::create($ledgerPivot);
            }
        }
    }

    public function changeInvoiceStatus($contractId){
        return Invoices::where(['contract_id' => $contractId,'is_invoice_paid' => 0])
            ->update(['status' => 'Uncollectible']);
    }

    public function addAdhocTicketAmount(Request $request){
        try{
            DB::beginTransaction();
            $validate = Validator::make($request->all(), [
                'ticket_id' => 'required',
                'amount' => 'required',
                'payment_mode' => 'required',
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }
            $addTicketAmount = [
                'ticket_id' => $request->ticket_id,
                'amount' => $request->amount,
                'payment_mode' => $request->payment_mode
            ];

            $addAmount = AdhocTicketAmount::create($addTicketAmount);
            DB::commit();
            return RestResponse::Success($addAmount,'Successfully Added Amount.');
        }catch (\Exception $e) {
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

}
