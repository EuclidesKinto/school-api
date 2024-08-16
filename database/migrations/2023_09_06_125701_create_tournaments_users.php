<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tournament_id")->unsigned();
            $table->foreignId("user_id")->unsigned();
            $table->foreign("tournament_id")->references('id')->on('tournaments');
            $table->foreign("user_id")->references('id')->on('users');
            $table->integer("value");
            $table->timestamps();
        });

        Schema::create('score_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId("score_id")->unsigned();
            $table->foreignId("user_id")->unsigned();
            $table->morphs("origin");
            $table->string("type");
            $table->integer("value");
            $table->integer("previous_score");
            $table->integer("current_score");
            $table->foreign("score_id")->references('id')->on('scores');
            $table->foreign("user_id")->references('id')->on('users');
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
        Schema::dropIfExists('tournaments_users');

        Schema::dropIfExists('score_history');
    }
}
