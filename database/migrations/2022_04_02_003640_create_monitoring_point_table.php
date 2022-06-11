<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringPointTable
 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_point', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 13)->nullable();
            $table->string('nombre', 100);
            $table->decimal('longitud', 12, 5);
            $table->decimal('latitud', 12, 5);
            $table->decimal('altitud', 12, 5);
            $table->tinyInteger('estado');
            $table->foreignId('project_id')->constrained('project');
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
        Schema::dropIfExists('monitoring_point');
    }
}
