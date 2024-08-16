<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCertificateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificate_user', function (Blueprint $table) {
            $table->string('url')->after('validation_id');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->string('description')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificate_user', function (Blueprint $table) {
            $table->dropColumn('url');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
