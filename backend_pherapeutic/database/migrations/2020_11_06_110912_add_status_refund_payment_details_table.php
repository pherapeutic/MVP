<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusRefundPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->enum('status', array('1', '2', '3', '4'))->default('1')->comment('1 => Hold payment, 2 => Payment done, 3 => Pay and Refund, 4 => Refund');
            $table->string('refund_id')->nullable();
            $table->string('refund_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('refund_id');
            $table->dropColumn('refund_amount');
        });
    }
}
