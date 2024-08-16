<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->renameColumn('comentable_type', 'commentable_type');
            $table->renameColumn('comentable_id', 'commentable_id');

            $table->longText('message')->change();
        });
    }


    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->renameColumn('commentable_type', 'comentable_type');
            $table->renameColumn('commentable_id', 'comentable_id');

            $table->string('message')->change();
        });
    }
}
