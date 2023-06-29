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
        Schema::table('contracts', function (Blueprint $table) {
            $table->renameColumn('is_active', 'open_ticket_contract')->default(1)->comment('0=CloseAllTicketContract, 1=OpenTicketContract');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->renameColumn('open_ticket_contract', 'is_active')->default(1)->comment('0=Inactive, 1=Active');
        });
    }
};
