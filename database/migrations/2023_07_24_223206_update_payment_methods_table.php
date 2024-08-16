<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->string('brand')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('display_number')->nullable();
            $table->bigInteger('bin')->nullable();
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn('brand');
            $table->dropColumn('holder_name');
            $table->dropColumn('display_number');
            $table->dropColumn('bin');
            $table->dropColumn('year');
            $table->dropColumn('month');
        });
    }
}
