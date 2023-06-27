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
        if(Schema::hasTable('users')){
            Schema::table('users', function (Blueprint $table){
                $table->dropColumn('email_verified_at');
                $table->dropColumn('company_name');
                $table->dropColumn('address');
                $table->dropColumn('area');
                $table->dropColumn('zipcode');
                $table->dropColumn('city');
                $table->dropColumn('state');
                $table->dropColumn('country');
                $table->dropColumn('remember_token');
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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();
            $table->string('area')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->rememberToken();
        });
    }
};
