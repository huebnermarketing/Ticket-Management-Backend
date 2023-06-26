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
        if(Schema::hasTable('ticket_statuses')){
            Schema::table('ticket_statuses', function (Blueprint $table){
                $table->string('text_color')->after('status_name');
                $table->string('background_color')->after('text_color');
            });
        }

        if(Schema::hasTable('ticket_priorities')){
            Schema::table('ticket_priorities', function (Blueprint $table){
                $table->string('text_color')->after('priority_name');
                $table->string('background_color')->after('text_color');
            });
        }

        if(Schema::hasTable('payment_types')){
            Schema::table('payment_types', function (Blueprint $table){
                $table->string('text_color')->after('payment_type');
                $table->string('background_color')->after('text_color');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_statuses', function (Blueprint $table) {
            $table->dropColumn('text_color');
            $table->dropColumn('background_color');
        });

        Schema::table('ticket_priorities', function (Blueprint $table) {
            $table->dropColumn('text_color');
            $table->dropColumn('background_color');
        });

        Schema::table('payment_types', function (Blueprint $table) {
            $table->dropColumn('text_color');
            $table->dropColumn('background_color');
        });
    }
};
