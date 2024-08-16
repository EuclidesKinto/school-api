<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('ami_id')->index();
            $table->string('os_name');
            $table->foreignId('tournament_id');
            $table->enum('dificulty', ['easy', 'medium', 'hard', 'insane']);
            $table->string('type')->onDelete('cascade');
            $table->boolean('active')->default(0);
            $table->boolean('is_freemium')->default(0);
            $table->text('photo_path')->nullable();

            $table->string('description')->nullable();
            $table->dateTime('release_at')->nullable();
            $table->dateTime('retire_at')->nullable();

            $table->timestamps();

            $table->foreignId('blooder_id')->nullable(); // First Blood User
            $table->foreign('blooder_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreignId('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machines');
    }
}
