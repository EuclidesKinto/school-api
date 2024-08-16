<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlooderidChallengesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('challenges', function (Blueprint $table) {

            $table->foreignId('blooder_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('tournament_id')->nullable()->references('id')->on('tournaments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->dropForeign(['blooder_id']);
            $table->dropForeign(['tournament_id']);
            $table->dropColumn(['blooder_id']);
            $table->dropColumn(['tournament_id']);
        });
    }
}
