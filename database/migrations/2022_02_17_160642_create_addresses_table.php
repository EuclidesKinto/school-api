<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('line_1')->index();
            $table->string('line_2')->index()->nullable();
            $table->string('state');
            $table->string('city')->index();
            $table->string('zip_code')->index();
            $table->foreignId('user_id');
            $table->foreignId('billing_profile_id')->nullable();
            $table->enum('type', ['billing', 'home', 'shipping', 'other'])->default('billing');
            $table->string('country')->default('BR');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('billing_profile_id')->references('id')->on('billing_profiles')->onDelete('cascade');
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
        Schema::dropIfExists('addresses');
    }
}
