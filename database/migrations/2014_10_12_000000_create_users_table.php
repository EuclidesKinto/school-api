<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->text('profile_photo_path')->nullable();
            $table->string('google_id')->nullable()->unique()->index();
            $table->string('last_login')->nullable();

            $table->integer('level')->default(0)->nullable();
            $table->integer('xp')->default(0)->nullable();
            $table->string('bio')->nullable();
            $table->string('cpf')->nullable();
            $table->string('nick')->nullable();
            $table->string('site')->nullable();;
            $table->json('metadata')->nullable();
            $table->string('country')->nullable();
            $table->string('language')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
