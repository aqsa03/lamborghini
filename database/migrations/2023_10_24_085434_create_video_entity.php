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
            $table->unsignedBigInteger('video_preview_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('model_id');
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
