<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->renameColumn('dificulty', 'difficulty');
            $table->datetime('release_at')->nullable();
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->string('image_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->renameColumn('difficulty', 'dificulty');
            $table->dropColumn('release_at');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
}
