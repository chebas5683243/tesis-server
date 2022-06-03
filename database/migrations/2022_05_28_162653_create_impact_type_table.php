<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImpactTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('impact_type', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion');
        });

        Schema::table('environmental_impact', function (Blueprint $table) {
            $table->foreignId('impact_type_id')->constrained('impact_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('impact_type');
        
        Schema::table('environmental_impact', function (Blueprint $table) {
            $table->dropColumn('impact_type_id');
        });
    }
}
