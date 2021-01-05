<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
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
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('stripe_id')->nullable();
            $table->enum('role', array('0', '1', '2'))->default('0')->comment('0 => Client,  1 => Therapist, 2 => Admin');
            $table->string('temp_email')->nullable();
            $table->string('verification_otp')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('reset_password_token')->nullable();
            $table->enum('is_pro_bono_work', array('0', '1'))->default('0')->comment('0 => No,  1 => Yes');
            $table->string('fcm_token')->nullable();
            $table->enum('device_type', array('0', '1'))->default('0')->comment('0 => Android,  1 => IOS');
            $table->enum('notification_status', array('0', '1'))->default('1')->comment('0 => Off,  1 => On');
            $table->enum('online_status', array('0', '1'))->default('1')->comment('0 => Off,  1 => On');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
