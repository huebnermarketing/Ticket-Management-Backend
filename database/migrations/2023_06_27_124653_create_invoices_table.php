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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contract_id');
            $table->double('total_amount');
            $table->double('paid_amount')->default(0);
            $table->double('outstanding_amount');
            $table->enum('status',['Paid','Partially Paid','Unpaid'])->default('Unpaid')->comment('Paid,Partially Paid,Unpaid');
            $table->tinyInteger('is_invoice_paid')->default(0)->comment('0=Not paid, 1=Paid');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->softDeletes();
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
        Schema::dropIfExists('invoices');
    }
};
