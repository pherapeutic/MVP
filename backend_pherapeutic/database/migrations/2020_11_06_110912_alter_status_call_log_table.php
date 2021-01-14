<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusCallLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `call_logs` CHANGE `payment_status` `payment_status` ENUM('1', '2', '3') NOT NULL DEFAULT '1' COMMENT '1 => Hold payment, 2 => Payment done, 3=> Refund';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `call_logs` CHANGE `payment_status` `payment_status` ENUM('1', '2') NOT NULL DEFAULT '1' COMMENT '1 => Hold payment, 2 => Payment done';");
    }
}
