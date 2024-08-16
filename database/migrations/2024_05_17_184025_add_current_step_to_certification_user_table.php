<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentStepToCertificationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certification_user', function (Blueprint $table) {
            $table->unsignedTinyInteger('current_step')->default(1)->after('deadline_send_report');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certification_user', function (Blueprint $table) {
            $table->dropColumn('current_step');
        });
    }
}
