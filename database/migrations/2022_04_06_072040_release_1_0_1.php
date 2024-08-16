<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Release101 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * All database changes on the for the relese 1.0.1 should be done here.
         */

        Schema::table('users', function (Blueprint $table) {
            $table->string('vpn_user_id')->nullable();
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->enum('dificulty', ['easy', 'medium', 'hard', 'insane'])->nullable();
            $table->foreignId('creator_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
        });

        /**
         * Update owns table to store user progress on machines
         * 
         */
        Schema::table('owns', function (Blueprint $table) {
            $table->integer('progress')->default(0);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->string('video_unique_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('owns', function (Blueprint $table) {
            $table->dropColumn('progress');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('vpn_user_id');
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->dropColumn('difficulty');
            $table->dropConstrainedForeignId('creator_id');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('video_unique_id');
        });
    }
}
