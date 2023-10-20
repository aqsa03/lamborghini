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
            $table->string('search_string')->nullable();
        });
        Schema::table('seasons', function (Blueprint $table) {
            $table->string('search_string')->nullable();
        });
        Schema::table('episodes', function (Blueprint $table) {
            $table->string('search_string')->nullable();
        });
        Schema::table('lives', function (Blueprint $table) {
            $table->string('search_string')->nullable();
        });
        Schema::table('news', function (Blueprint $table) {
            $table->string('search_string')->nullable();
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
            $table->dropColumn(['search_string']);
        });
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn(['search_string']);
        });
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropColumn(['search_string']);
        });
        Schema::table('lives', function (Blueprint $table) {
            $table->dropColumn(['search_string']);
        });
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['search_string']);
        });
    }
};
