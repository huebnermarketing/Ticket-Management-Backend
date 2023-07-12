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
        Schema::create('ledger_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ledger_unique_id');
            $table->unsignedBigInteger('contract_id');
            $table->date('date');
            $table->double('ledger_amount');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->enum('payment_mode',['card','cash','online'])->comment('card,cash,online')->nullable();
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
        Schema::dropIfExists('ledger_invoices');
    }
};
