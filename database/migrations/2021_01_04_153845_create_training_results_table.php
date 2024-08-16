<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->nullable();
            $table->foreignId('question_id')->nullable();
            $table->foreignId('answer_id')->nullable();
            $table->boolean('correct')->default(0);

            $table->foreign('training_id')->references('id')->on('trainings')->cascadeOnDelete();
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
            $table->foreign('answer_id')->references('id')->on('answers')->cascadeOnDelete();
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
        Schema::dropIfExists('training_results');
    }
}
