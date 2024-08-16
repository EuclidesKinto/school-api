<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certification_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('certification_id');
            $table->datetime('deadline');
            $table->string('user_report')->nullable();
            $table->string('url')->nullable();
            $table->string('comment')->nullable();
            $table->integer('grade')->nullable();
            $table->boolean('approved')->nullable();
            $table->uuid('validation_id')->nullable();
            $table->datetime('timeout')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('certification_id')->references('id')->on('certifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certification_user');
    }
}
