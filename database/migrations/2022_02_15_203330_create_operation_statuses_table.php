<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('description')->nullable();
            // categoria de status, ex: pagamento com CC, atualização do pedido, etc. ['order', 'payment', 'transaction']
            $table->string('category')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('operation_statuses');
    }
}
