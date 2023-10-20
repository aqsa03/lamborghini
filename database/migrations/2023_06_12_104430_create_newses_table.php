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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status', 50);
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('tags')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->unsignedBigInteger('news_category_id');
            $table->unsignedBigInteger('video_id')->nullable();
            $table->unsignedBigInteger('video_preview_id')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
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
        Schema::dropIfExists('news');
    }
};
