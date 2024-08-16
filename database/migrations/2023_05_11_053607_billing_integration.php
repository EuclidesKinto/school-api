<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BillingIntegration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('payment_gw_id')->nullable();
            $table->dropForeign(['subscription_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['invoice_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['subscription_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['charge_id']);
            $table->dropForeign(['invoice_id']);
        });

        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
        });

        Schema::table('plan_subscription_usage', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
        });

        Schema::dropIfExists('payment_methods');
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method_id');
            $table->string('payment_gw_user_id');
            $table->foreignId('user_id');
            $table->boolean('default');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::dropIfExists('plans');
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identifier');
            $table->integer('interval_months');
            $table->integer('value_cents');
            $table->timestamps();
        });

        Schema::dropIfExists('subscriptions');
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id');
            $table->foreignId('user_id');
            $table->string('status');
            $table->string('expires_at');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::dropIfExists('invoices');
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->datetime('due_date')->nullable();
            $table->string('status');
            $table->integer('value_cents');
            $table->string('payment_gw_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::dropIfExists('payers');
        Schema::create('payers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->bigInteger('cpf_cnpj');
            $table->string('name');
            $table->integer('phone_prefix');
            $table->bigInteger('phone');
            $table->string('email');
            $table->string('street');
            $table->integer('number');
            $table->string('district');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::dropIfExists('charges');
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('invoice_id');
            $table->integer('value_cents');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('payment_gw_id');
        });

        Schema::dropIfExists('payment_methods');

        Schema::dropIfExists('subscriptions');

        Schema::dropIfExists('plans');

        Schema::dropIfExists('invoices');

        Schema::dropIfExists('payers');

        Schema::dropIfExists('charges');
    }
}
