<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Release104 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('user_id');
            $table->foreignId('achievable_id');
            $table->string('achievable_type');
            $table->integer('time_to_complete');
            $table->string('image_path');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('achievements');
    }
}
