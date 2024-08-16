<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Release103 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('code')->unique()->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->foreignId('payment_method_id')->nullable();

            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->dateTime('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dateTime('paid_at')->nullable();
        });

        Schema::table('billing_profiles', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->renameColumn('metadata', 'details');
            $table->renameColumn('card_id', 'gateway_id');
        });

        Schema::table('order_updates', function (Blueprint $table) {
            $table->dropForeign(['operation_status_id']);
            $table->dropColumn('operation_status_id');
            $table->string('status')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable();
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('transaction_id', 'gateway_id')->nullable()->change();
            // $table->string('gateway_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('gateway_id', 'subscription_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn('subscription_id');
        });

        Schema::table('order_updates', function (Blueprint $table) {
            $table->foreignId('operation_status_id')->nullable();
            $table->foreign('operation_status_id')->references('id')->on('operation_statuses')->onDelete('cascade');
            $table->dropColumn('status');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->renameColumn('details', 'metadata');
            $table->renameColumn('gateway_id', 'card_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('paid_at');
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn('paid_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('paid_at');
            $table->dropColumn('refunded_at');
            $table->dropColumn('payment_method_id');
            $table->dropForeign(['payment_method_id']);
        });
    }
}
