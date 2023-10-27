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
        //
        Schema::table('CarModel', function (Blueprint $table) {
            $table->string('status', 50);
            $table->timestamp('published_at')->nullable();
            $table->dropColumn('image_id');
            $table->renameColumn('qr_scan_id', 'qr_code_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::create('CarModel', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->nullable();
            $table->dropColumn('status');
            $table->dropColumn('published_at');
        });
    }
};
