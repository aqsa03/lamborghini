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
        \DB::table('ModelVideo')->whereNotNull('ce_text')->update([
            'ce_text' => \DB::raw('JSON_OBJECT("ce_text", ce_text)'),
        ]);
        Schema::table('ModelVideo', function (Blueprint $table) {
            $table->json('ce_text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('ModelVideo')->whereNotNull('ce_text')->update([
            'ce_text' => \DB::raw('JSON_UNQUOTE(JSON_EXTRACT(ce_text, "$.ce_text"))'),
        ]);
        Schema::table('ModelVideo', function (Blueprint $table) {
            $table->string('ce_text', 255)->nullable()->change();
        });
    }
};