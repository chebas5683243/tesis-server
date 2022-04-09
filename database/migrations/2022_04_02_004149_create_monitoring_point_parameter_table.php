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
            $table->float('valor_maximo')->nullable();
            $table->tinyInteger('tiene_minimo');
            $table->float('valor_minimo')->nullable();
            $table->float('aqi_1')->nullable();
            $table->float('aqi_2')->nullable();
            $table->float('aqi_3')->nullable();
            $table->float('aqi_4')->nullable();
            $table->float('aqi_5')->nullable();
            $table->float('valor_ideal')->nullable();
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
