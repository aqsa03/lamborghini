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
        Schema::create('palimpsest_template_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('day');
            $table->time('start_at');
            $table->time('end_at');

            $table->unsignedBigInteger('live_id');
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('episode_id')->nullable();
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
        Schema::dropIfExists('palimpsest_template_items');
    }
};
