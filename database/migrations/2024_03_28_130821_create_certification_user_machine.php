<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificationUserMachine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certification_user_machine', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_user_id');
            $table->foreignId('machine_id');
            $table->timestamps();

            $table->foreign('certification_user_id')->references('id')->on('certification_user');
            $table->foreign('machine_id')->references('id')->on('machines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certification_user_machine');
    }
}
