<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringPointParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_point_parameter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_point_id')->constrained('monitoring_point');
            $table->foreignId('parameter_id')->constrained('parameter');
            $table->tinyInteger('tiene_maximo');
            $table->decimal('valor_maximo', 12, 3)->nullable();
            $table->tinyInteger('tiene_minimo');
            $table->decimal('valor_minimo', 12, 3)->nullable();
            $table->decimal('aqi_1', 12, 3)->nullable();
            $table->decimal('aqi_2', 12, 3)->nullable();
            $table->decimal('aqi_3', 12, 3)->nullable();
            $table->decimal('aqi_4', 12, 3)->nullable();
            $table->decimal('aqi_5', 12, 3)->nullable();
            $table->decimal('valor_ideal', 12, 3)->nullable();
            $table->tinyInteger('usa_estandar')->default(1);
            $table->tinyInteger('usa_aqi');
            $table->tinyInteger('usa_wqi');
            $table->tinyInteger('no_aplica');
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
        Schema::dropIfExists('monitoring_point_parameter');
    }
}
