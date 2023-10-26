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
        Schema::create('ModelVideo', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status', 50);
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('video_id')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('video_preview_id')->nullable();
            $table->json('tags')->nullable();
            $table->json('related')->nullable();
            $table->boolean('is_360')->default(false);
            $table->boolean('vod')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('model_id')->references('id')->on('CarModel');
            $table->foreign('category_id')->references('id')->on('categories');
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
        Schema::dropIfExists('ModelVideo');
    }
};
