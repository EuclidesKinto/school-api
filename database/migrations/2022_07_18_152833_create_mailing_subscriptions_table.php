<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->nullable(); // id do mailing_contact na base local (hacking club)
            $table->foreignId('list_id')->nullable(); // id da mailing_list na base local (hacking club)
            $table->string('provider')->nullable(); // activecampaign, mailchimp, etc.
            $table->string('status')->nullable(); // active, unsubscribed, etc.
            $table->string('subscription_id')->nullable(); // id da subscription no provedor de mailing.
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('mailing_contacts')->onDelete('cascade');
            $table->foreign('list_id')->references('id')->on('mailing_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailing_subscriptions');
    }
}
