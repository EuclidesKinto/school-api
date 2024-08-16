<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->string('title')->notNullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(0);
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->renameColumn('course_id', 'module_id');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });

        Schema::create('lesson_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('lesson_id')->references('id')->on('lessons');
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

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->renameColumn('module_id', 'course_id');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        Schema::dropIfExists('modules');
    }
}
