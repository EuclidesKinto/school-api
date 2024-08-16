<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('count')->default(1);
            $table->double('amount')->default(0.00);
            $table->foreignId('order_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('transaction_id')->nullable();
            $table->foreignId('payer_id')->nullable();
            $table->foreignId('payment_method_id')->nullable(); // para relacionar com o payment method do usuário que pagou
            $table->string('status')->nullable(); // sempre deve refletir o status da cobrança no gateway
            $table->string('payment_method')->nullable(); // cartão, boleto, pix // para exibir no front ou algo assim.
            $table->string('gateway')->nullable(); // stripe, pagarme, paypal
            $table->string('gateway_code')->nullable(); // identificador da cobrança no gateway
            $table->string('gateway_id')->nullable(); // identificador da cobrança no gateway
            $table->string('gateway_payer_id')->nullable(); // identificador do usuário no gateway
            $table->string('currency')->nullable(); // moeda da cobrança
            $table->json('details')->nullable(); // detalhes da cobrança no gateway
            $table->datetime('due_at')->nullable(); // data limite para pagamento
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('payer_id')->references('id')->on('billing_profiles')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charges');
    }
}
