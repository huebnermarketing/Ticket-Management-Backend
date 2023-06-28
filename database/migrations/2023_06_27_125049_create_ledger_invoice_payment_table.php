<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ledger_invoice_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('ledger_invoice_id');
            $table->unsignedBigInteger('contract_id');
            $table->double('adjustable_amount');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('ledger_invoice_id')->references('id')->on('ledger_invoices');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ledger_invoice_payments');
    }
};
