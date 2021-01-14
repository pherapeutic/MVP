<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('appointment_id');
            $table->string('charge_id')->nullable();
            $table->string('txn_id')->nullable();
            $table->string('amount')->nullable();
            $table->enum('is_captured', array('0', '1'))->default('0')->comment('0 => No,  1 => Yes');
            $table->string('card_id')->nullable();
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
        Schema::dropIfExists('payment_details');
    }
}
