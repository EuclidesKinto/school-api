<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChallengeInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenge_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id');
            $table->foreignId('user_id');
            $table->boolean('is_active')->default(true);
            $table->timestamp('startup')->nullable();
            $table->timestamp('shutdown')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('docker_container_id')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('challenge_id')->references('id')->on('challenges')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challenge_instances');
    }
}
