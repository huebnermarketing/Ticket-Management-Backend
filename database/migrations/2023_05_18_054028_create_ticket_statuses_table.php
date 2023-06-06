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
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status_name');
            $table->tinyInteger('is_active')->default(0)->comment('0=Inactive, 1=Active');
            $table->tinyInteger('is_lock')->default(0)->comment('0=Unlock, 1=Lock');
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
        Schema::dropIfExists('ticket_statuses');
    }
};
