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
        Schema::table('programs', function (Blueprint $table) {
            $table->unsignedBigInteger('image_poster_id')->nullable();
        });
        Schema::table('seasons', function (Blueprint $table) {
            $table->unsignedBigInteger('image_poster_id')->nullable();
        });
        Schema::table('episodes', function (Blueprint $table) {
            $table->unsignedBigInteger('image_poster_id')->nullable();
        });
        Schema::table('lives', function (Blueprint $table) {
            $table->unsignedBigInteger('image_poster_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['image_poster_id']);
        });
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn(['image_poster_id']);
        });
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropColumn(['image_poster_id']);
        });
        Schema::table('lives', function (Blueprint $table) {
            $table->dropColumn(['image_poster_id']);
        });
    }
};
