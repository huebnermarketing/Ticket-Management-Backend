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
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('ticket_type',['adhoc','contract'])->default('adhoc')->comment('adhoc,contract');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customer_locations_id');
            $table->unsignedBigInteger('problem_type_id');
            $table->unsignedBigInteger('ticket_status_id');
            $table->unsignedBigInteger('assigned_user_id');
            $table->unsignedBigInteger('appointment_type_id');
            $table->unsignedBigInteger('payment_type_id');

            $table->string('problem_title');
            $table->dateTime('due_date');
            $table->longText('description');
            $table->unsignedDouble('amount', 8, 2);
            $table->unsignedDouble('collected_amount', 8, 2);
            $table->unsignedDouble('remaining_amount', 8, 2);
            $table->enum('payment_mode',['card','cash','online'])->default('card')->comment('card,cash,online');

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('customer_locations_id')->references('id')->on('customer_locations');
            $table->foreign('problem_type_id')->references('id')->on('problem_types');
            $table->foreign('ticket_status_id')->references('id')->on('ticket_statuses');
            $table->foreign('assigned_user_id')->references('id')->on('users');
            $table->foreign('appointment_type_id')->references('id')->on('appointment_types');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
