<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(); // id usuário na base local (hacking club)
            $table->string('contact_id')->nullable(); // contact_id on mailing provider's system.
            $table->string('provider')->nullable(); // activecampaign, mailchimp, etc.
            $table->string('details')->nullable(); // JSON encoded details.
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailing_contacts');
    }
}
