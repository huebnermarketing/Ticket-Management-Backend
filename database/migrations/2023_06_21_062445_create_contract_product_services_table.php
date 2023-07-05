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
        Schema::create('contract_product_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unique_id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('product_service_id');
            $table->unsignedDouble('product_qty');
            $table->unsignedDouble('product_amount',8,2);

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('product_service_id')->references('id')->on('product_services')->onDelete('cascade');
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
        Schema::dropIfExists('contract_product_services');
    }
};
