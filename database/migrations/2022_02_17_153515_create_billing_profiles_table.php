<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('document')->nullable();
            $table->string('document_type')->nullable();
            $table->string('gender')->nullable();
            $table->string('email')->nullable();
            $table->date('birthdate')->nullable();
            $table->json('phones')->nullable();
            $table->json('metadata')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('email');
        });

        /**
         * Add billing profile to orders table
         */
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payer_id')->nullable();
            $table->foreign('payer_id')->references('id')->on('billing_profiles')->onDelete('cascade');
        });

        /**
         * Add billing profile to transactions table
         */
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('payer_id')->nullable();
            $table->foreign('payer_id')->references('id')->on('billing_profiles')->onDelete('cascade');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->foreignId('billing_profile_id')->nullable();
            $table->foreign('billing_profile_id')->references('id')->on('billing_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_profiles');
    }
}
