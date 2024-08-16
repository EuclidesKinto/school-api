<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id');
            $table->foreignId('user_id');
            $table->boolean('is_active')->default(true);
            $table->timestamp('startup')->nullable();
            $table->timestamp('shutdown')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('aws_instance_id')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('machine_id')->references('id')->on('machines')->cascadeOnDelete();
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
        Schema::dropIfExists('instances');
    }
}
