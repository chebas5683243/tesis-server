<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnImpactoFromEnvironmentalImpactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('environmental_impact', function (Blueprint $table) {
            $table->dropColumn('impacto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('environmental_impact', function (Blueprint $table) {
            $table->string('impacto', 100);
        });
    }
}
