<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncidentType extends Migration
{
    public function up()
    {
        print("Migración de todas las tablas: Iniciando Incident\n");
        Schema::disableForeignKeyConstraints();
        Schema::create('personal', function(Blueprint $table) {
            $table->id();
            $table->string('nombre_completo',200);
            $table->string('email', 100);
        });
        Schema::create('incident_type', function(Blueprint $table) {
            $table->id();
            $table->string('nombre',200);
            $table->tinyInteger('estado_alerta');
        });
        Schema::create('incident_type_alert', function(Blueprint $table) {
            $table->id();
        });
        Schema::create('incident_type_parameter', function(Blueprint $table) {
            $table->id();
        });
        //foreign keys
        Schema::table('incident_type_alert', function (Blueprint $table) {
            $table->foreignId('personal_id')->constrained('personal');
            $table->foreignId('incident_type_id')->constrained('incident_type');
        });
        Schema::table('incident_type_parameter', function (Blueprint $table) {
            $table->foreignId('parameter_id')->constrained('parameter');
            $table->foreignId('incident_type_id')->constrained('incident_type');
        });
        Schema::enableForeignKeyConstraints();
        print("Finalizado Incident!\n");
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('personal');
        Schema::dropIfExists('incident_type');
        Schema::dropIfExists('incident_type_alert');
        Schema::dropIfExists('incident_type_parameter');
        Schema::enableForeignKeyConstraints();
    }
}
