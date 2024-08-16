<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->nullable();
            $table->string('webhook_id')->index()->nullable(); // id do webhook no pagarme ou stripe
            $table->string('model')->index()->nullable(); // model do objeto que gerou o webhook ['charge', 'subscription', 'invoice', 'order' ...]
            $table->string('event')->index()->nullable(); // evento que o webhook foi disparado
            $table->timestamp('timestamp')->nullable(); // created_at do webhook no pagarme ou stripe
            $table->json('data')->nullable(); // o campo data do webhook no pagarme ou stripe
            $table->jsonb('raw_data')->nullable(); // o webhook no pagarme ou stripe inteiro
            $table->string('status')->default('received')->index(); // status do webhook localmente ['received', 'processed', 'failed']
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
        Schema::dropIfExists('webhooks');
    }
}
