<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quizz_id')->nullable();
            $table->string('text'); // o texto da pergunta
            // em caso de perguntas de múltipla escolha, é possível ter várias respostas associadas à pergunta
            // porém apenas uma delas estará correta.
            // caso a pergunta tenha apenas uma única resposta, não fará diferença. (este modelo se encaixa em ambos os casos)
            $table->foreignId('answer_id')->nullable();
            $table->timestamps();

            $table->foreign('quizz_id')->references('id')->on('quizzes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
