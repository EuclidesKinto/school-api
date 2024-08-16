<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Release102 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->integer('installments')->default(1)->nullable();
            $table->decimal('subtotal', 12, 2, true)->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('gateway')->default('pagarme')->nullable();
            $table->renameColumn('pagarme_invoice_id', 'gateway_invoice_id')->nullable();
            $table->renameColumn('pagarme_url', 'gateway_url')->nullable();
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->renameColumn('amount', 'value')->nullable();
            $table->dropColumn('amount_type');
            $table->enum('type', ['flat', 'percentage'])->default('flat');
            $table->unsignedInteger('cycles')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dropColumn('quantity');
            $table->unsignedInteger('limit')->nullable();
        });

        Schema::create('coupon_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id');
            $table->foreignId('product_id');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('coupon_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id');
            $table->foreignId('user_id');

            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('amount', 'total')->nullable();
            $table->double('subtotal', 12, 2)->nullable();
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->string('gateway')->default('pagarme')->nullable();
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable();
            $table->foreignId('invoice_id')->nullable();
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable();
            $table->foreignId('charge_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('charge_id')->references('id')->on('charges')->onDelete('cascade');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false);
            $table->string('gateway_id')->nullable();
            $table->string('gateway')->default('pagarme')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('is_paid');
            $table->dropColumn('gateway_id');
            $table->dropColumn('gateway');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['charge_id']);
            $table->dropColumn('invoice_id');
            $table->dropColumn('charge_id');
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('subscription_id');
            $table->dropColumn('invoice_id');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn('gateway');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('total', 'amount')->nullable();
            $table->dropColumn('subtotal');
        });

        Schema::drop('coupon_user');

        Schema::drop('coupon_product');

        Schema::table('coupons', function (Blueprint $table) {
            $table->renameColumn('value', 'amount')->nullable();
            $table->dropColumn('settings');
            $table->dropColumn('cycles');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('gateway');
            $table->renameColumn('gateway_invoice_id', 'pagarme_invoice_id')->nullable();
            $table->renameColumn('gateway_url', 'pagarme_url')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('installments');
            $table->dropColumn('subtotal');
        });
    }
}
