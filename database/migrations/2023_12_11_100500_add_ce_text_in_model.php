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
        Schema::table('CarModel', function (Blueprint $table) {
            //
            $table->string('ce_text', 1000)->default('Fuel consumption and emission values of all vehicles promoted on this page*: Fuel consumption combined: 14,1-12,7 l/100km (WLTP); CO2-emissions combined: 325-442 g/km (WLTP); Under approval, not available for sale: Revuelto; Concept car, not available for sale: Asterion, Estoque');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('CarModel', function (Blueprint $table) {
            //
            $table->dropColumn('ce_text');
        });
    }
};
