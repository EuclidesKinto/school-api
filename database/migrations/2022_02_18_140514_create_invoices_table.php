<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('order_id');
            $table->string('code')->nullable();
            $table->foreignId('billing_profile_id')->nullable();
            $table->foreignId('transaction_id')->nullable();
            $table->foreignId('subscription_id')->nullable();
            $table->string('pagarme_invoice_id')->index()->nullable();
            $table->string('pagarme_url')->nullable();
            $table->double('amount', 12, 2)->default(0.00);
            $table->foreignId('payment_method_id')->nullable();
            $table->integer('installments')->default(1);
            $table->string('status')->default('pending');
            $table->dateTime('billing_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('seen_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->dateTime('billing_period_start_at')->nullable();
            $table->dateTime('billing_period_end_at')->nullable();
            $table->double('total_discount', 12, 2)->nullable();
            $table->double('total_increment', 12, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('billing_profile_id')->references('id')->on('billing_profiles')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
