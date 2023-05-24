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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            //$table->enum('user_type',['owner','admin','staff'])->default('staff')->comment('owner,admin,staff');
            $table->string('phone');
            $table->string('company_name');
            $table->string('address');
            $table->string('area');
            $table->string('zipcode');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('profile_photo');
            $table->tinyInteger('is_active')->default(0)->comment('0=Inactive, 1=Active');
            $table->tinyInteger('is_verified')->default(0)->comment('0=Not Verify, 1=Verify');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
