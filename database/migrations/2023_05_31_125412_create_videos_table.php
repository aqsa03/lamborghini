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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('podcast')->default(false);
            $table->string('source_url')->nullable();
            $table->string('image_source_url')->nullable();
            $table->unsignedInteger('source_width')->nullable();
            $table->unsignedInteger('source_height')->nullable();
            $table->boolean('public')->default(false);
            $table->string('url')->nullable();
            $table->string('url_mp4')->nullable();
            $table->string('image_preview_url')->nullable();
            $table->string('meride_status', 50); // saved|pending|ready|error
            $table->unsignedBigInteger('meride_video_id')->nullable();
            $table->string('meride_embed_id', 50)->nullable();
            $table->longText('log')->nullable();
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
        Schema::dropIfExists('videos');
    }
};
