<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('caller_id');
            $table->bigInteger('user_id');
            $table->bigInteger('therapist_id');
            $table->enum('call_status', array('0', '1', '2', '3'))->default('0')->comment('0 => Connecting, 1 => Inprogress, 2 => Ended, 3 => Decline');
            $table->enum('payment_status', array('1', '2'))->default('1')->comment('1 => Hold payment, 2 => Payment done');
            $table->dateTime('ended_at')->nullable();
            $table->string('duration')->nullable();
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
        Schema::dropIfExists('call_logs');
    }
}
