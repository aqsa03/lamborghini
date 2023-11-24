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
            $table->json('models')->nullable();
            $table->string('thumb_num')->nullable();
            $table->renameColumn('is_360', 'ext_view');
            $table->renameColumn('360_video', 'ext_view_url');
          
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
            $table->json('models')->nullable();
            $table->string('thumb_num')->nullable();
            $table->renameColumn('ext_view', 'is_360');
            $table->renameColumn('ext_view_url', '360_video');
        });
    }
};
