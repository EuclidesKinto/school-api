<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLessonUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_user', function (Blueprint $table) {
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
        });

        Schema::table('challenge_owns', function (Blueprint $table) {
            $table->integer('progress')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lesson_user', function (Blueprint $table) {
            $table->dropColumn('started_at');
            $table->dropColumn('completed_at');
        });
        
        Schema::table('challenge_owns', function (Blueprint $table) {
            $table->dropColumn('progress');
        });
    }
}