<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagIdScoreHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('score_histories', function (Blueprint $table) {
            $table->foreignId("flag_id")->unsigned()->nullable()->after('score_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('score_histories', function (Blueprint $table) {
            $table->dropColumn('flag_id');
        });
    }
}
