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
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('stripe_id')->nullable();
            $table->enum('role', array('Client', 'Therapist','Admin'))->default('Client');
            $table->string('temp_email')->nullable();
            $table->string('verification_otp')->nullable();
            $table->string('email_verified_at')->nullable();
            $table->string('reset_password_token')->nullable();
            $table->boolean('is_pro_bono_work')->default(0);
            $table->string('fcm_token')->nullable();
            $table->enum('device_type', array('Android', 'IOS'))->nullable();
            $table->string('notification_status')->nullable();
            $table->string('online_status')->nullable();
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
