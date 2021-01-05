<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCapturedPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `payment_details` CHANGE `is_captured` `is_captured` ENUM('0', '1', '2', '3') COMMENT '0 => No, 1 => Yes, 2=> Refund, 3 => Pay and Refund';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `payment_details` CHANGE `is_captured` `is_captured` ENUM('0', '1') COMMENT '0 => No, 1 => Yes';");
    }
}
