<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateResourceIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("ALTER TABLE challenges MODIFY difficulty ENUM('easy', 'medium', 'hard', 'insane')");

        Schema::table('challenges', static function (Blueprint $table) {
            $table->string('remote_resource_id')->nullable();
            $table->boolean('is_freemium')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        DB::statement("ALTER TABLE challenges MODIFY difficulty VARCHAR(191)");

        Schema::table('challenges', static function (Blueprint $table) {
            $table->dropColumn('remote_resource_id');
            $table->text('difficulty')->change();
            $table->dropColumn('is_freemium');
        });
    }
}