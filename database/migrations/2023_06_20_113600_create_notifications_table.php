<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('message')->nullable();
            $table->string('image_url')->nullable();
            $table->string('name')->nullable();
            $table->string('topic')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('type')->nullable();
            $table->longText('log')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->string('android_notification_id')->nullable();
            $table->json('key_value_store')->nullable();
            $table->boolean('sound')->default(false);
            $table->string('tokenTargets')->nullable(); // intended as comma separated token targets
            $table->json('android_config')->nullable();
            $table->json('apns_config')->nullable();
            $table->json('webpush_config')->nullable();
            $table->json('fcm_config')->nullable();

            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('season_id')->nullable();
            $table->unsignedBigInteger('episode_id')->nullable();
            $table->unsignedBigInteger('live_id')->nullable();
            $table->unsignedBigInteger('news_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs');
            $table->foreign('season_id')->references('id')->on('seasons');
            $table->foreign('episode_id')->references('id')->on('episodes');
            $table->foreign('live_id')->references('id')->on('lives');
            $table->foreign('news_id')->references('id')->on('news');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
