<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('position')->nullable()->after('is_freemium');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->integer('position')->nullable()->after('active');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->integer('position')->nullable()->after('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('position');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('position');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
}
