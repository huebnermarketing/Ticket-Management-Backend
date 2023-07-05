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
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customer_location_id');
            $table->string('contract_title');
            $table->string('contract_details')->nullable();
            $table->unsignedDouble('amount',8,2);
            $table->unsignedBigInteger('duration_id');
            $table->unsignedBigInteger('payment_term_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->tinyInteger('is_auto_renew')->default(1)->comment('0=NotAutoRenew, 1=AutoRenew');
            $table->tinyInteger('is_active')->default(1)->comment('0=Inactive, 1=Active');
            $table->tinyInteger('is_archive')->default(0)->comment('0=NotArchive, 1=Archive');
            $table->tinyInteger('is_suspended')->default(0)->comment('0=NotSuspended, 1=Suspended');
            $table->unsignedDouble('remaining_amount',8,2);

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customer_location_id')->references('id')->on('customer_locations')->onDelete('cascade');
            $table->foreign('duration_id')->references('id')->on('contract_durations')->onDelete('cascade');
            $table->foreign('payment_term_id')->references('id')->on('contract_payment_terms')->onDelete('cascade');
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
        Schema::dropIfExists('contracts');
    }
};
