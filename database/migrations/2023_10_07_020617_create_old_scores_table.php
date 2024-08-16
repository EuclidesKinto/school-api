<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOldScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_scores', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('origin_type');
            $table->integer('origin_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('flag_id')->nullable();
            $table->string('resource');
            $table->string('flag');
            $table->boolean('first_blood');
            $table->dateTime('event_at');
            $table->boolean('imported')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('old_scores');
    }
}
