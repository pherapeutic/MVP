<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('therapist_id');
            $table->enum('status', array('0', '1', '2', '3', '4'))->default('0')->comment('0 => Waiting, 1 => Connected, 2 => Completed, 3 => Payment done, 4 => Disconnected');
            $table->enum('is_trail', array('0', '1'))->default('0')->comment('0 => No, 1 => Yes');
            $table->dateTime('ended_at')->nullable();
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
        Schema::dropIfExists('appointments');
    }
}
