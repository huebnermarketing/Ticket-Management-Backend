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
        if(Schema::hasTable('appointment_types')){
            Schema::table('appointment_types', function (Blueprint $table){
                $table->string('unique_id')->after('id');
            });
        }
        if(Schema::hasTable('payment_types')){
            Schema::table('payment_types', function (Blueprint $table){
                $table->string('unique_id')->after('id');
            });
        }
        if(Schema::hasTable('product_services')){
            Schema::table('product_services', function (Blueprint $table){
                $table->string('unique_id')->after('id');
            });
        }
        if(Schema::hasTable('ticket_priorities')){
            Schema::table('ticket_priorities', function (Blueprint $table){
                $table->string('unique_id')->after('id');
            });
        }
        if(Schema::hasTable('ticket_statuses')){
            Schema::table('ticket_statuses', function (Blueprint $table){
                $table->string('unique_id')->after('id');
            });
        }
        if(Schema::hasTable('problem_types')){
            Schema::table('problem_types', function (Blueprint $table){
                $table->string('unique_id')->after('id');
            });
        }
        if(Schema::hasTable('contract_statuses')){
            Schema::table('contract_statuses', function (Blueprint $table){
                $table->string('unique_id')->after('id');
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
        Schema::table('appointment_types', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
        Schema::table('payment_types', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
        Schema::table('product_services', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
        Schema::table('ticket_priorities', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
        Schema::table('ticket_statuses', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
        Schema::table('problem_types', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
        Schema::table('contract_statuses', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
    }
};
