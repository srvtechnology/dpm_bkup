<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOnlineChargesToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->double('online_charge_in_percent', 20, 4)->nullable();
            $table->double('online_charge_in_amount', 20, 4)->nullable();
            $table->double('total_amount', 20, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['online_charge_in_percent']);
            $table->dropColumn(['online_charge_in_amount']);
            $table->dropColumn(['total_amount']);
        });
    }
}
