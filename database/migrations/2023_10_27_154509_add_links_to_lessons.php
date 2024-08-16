<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinksToLessons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->text('links')->nullable();
        });

        Schema::table('hacktivities', function (Blueprint $table) {
            $table->boolean('is_fixed')->default(false);
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
            $table->dropColumn('links');
        });

        Schema::table('hacktivities', function (Blueprint $table) {
            $table->dropColumn('is_fixed');
        });
    }
}
