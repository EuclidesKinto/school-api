<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('owns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flag_id');
            $table->foreignId('user_id');
            $table->integer('points');
            $table->foreignId('instance_id');
            $table->foreignId('tournament_id');
            $table->foreignId('machine_id')->nullable();

            $table->timestamps();

            $table->foreign('flag_id')->references('id')->on('flags')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('instance_id')->references('id')->on('instances')->cascadeOnDelete();
            $table->foreign('tournament_id')->references('id')->on('tournaments')->cascadeOnDelete();
            $table->foreign('machine_id')->references('id')->on('machines')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('owns');
    }
}
