<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChallengeOwnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenge_owns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flag_id');
            $table->foreignId('user_id');
            $table->foreignId('course_id');
            $table->integer('points')->nullable();
            $table->foreignId('challenge_instance_id');
            $table->foreignId('tournament_id')->nullable();
            $table->foreignId('challenge_id');
            $table->foreignId('lesson_id')->nullable();

            $table->timestamps();

            $table->foreign('flag_id')->references('id')->on('flags')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->foreign('challenge_instance_id')->references('id')->on('challenge_instances')->cascadeOnDelete();
            $table->foreign('tournament_id')->references('id')->on('tournaments')->cascadeOnDelete()->nullable();
            $table->foreign('challenge_id')->references('id')->on('challenges')->cascadeOnDelete();
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challenge_owns');
    }
}
