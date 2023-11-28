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
        Schema::table('ModelVideo', function (Blueprint $table) {
            //
            $table->boolean('product_video')->nullable();
            $table->boolean('subtitles')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ModelVideo', function (Blueprint $table) {
            //
            $table->dropColumn('product_video');
            $table->dropColumn('subtitles');
        });
    }
};
