<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllTables extends Migration
{
    public function up()
    {
        print("Migración de todas las tablas: Iniciando\n");
        Schema::disableForeignKeyConstraints();
        Schema::create('company', function(Blueprint $table) {
            $table->id();
            $table->string('ruc',11);
            $table->string('razon_social',100);
            $table->string('tipo_contribuyente', 100);
            $table->string('direccion_fiscal', 200);
            $table->string('distrito_ciudad', 100);
            $table->string('departamento', 45);
            $table->string('email', 100);
            $table->string('numero_telefonico', 20);
            $table->tinyInteger('es_propia');
            $table->tinyInteger('estado');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('parameter', function(Blueprint $table) {
            $table->id();
            $table->string('nombre',50);
            $table->string('nombre_corto',50);
            $table->tinyInteger('tiene_maximo');
            $table->float('valor_maximo')->nullable();
            $table->tinyInteger('tiene_minimo');
            $table->float('valor_minimo')->nullable();
        });
        Schema::create('phase', function(Blueprint $table) {
            $table->id();
            $table->string('nombre',50);
            $table->string('descripcion',200);
            $table->integer('estado');
            $table->dateTime('inicio')->nullable();
            $table->dateTime('fin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('project', function(Blueprint $table) {
            $table->id();
            $table->string('nombre',200);
            $table->text('descripcion');
            $table->string('codigo',13)->nullable();
            $table->datetime('fecha_inicio');
            $table->datetime('fecha_fin_tentativa')->nullable();
            $table->datetime('fecha_fin')->nullable();
            $table->string('ubicacion',100);
            $table->integer('estado');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('unit_measurement', function(Blueprint $table) {
            $table->id();
            $table->string('nombre',50);
            $table->string('nombre_corto',50);
        });
        Schema::create('user', function(Blueprint $table) {
            $table->id();
            $table->string('primer_nombre', 45);
            $table->string('segundo_nombre', 45);
            $table->string('primer_apellido', 45);
            $table->string('segundo_apellido', 45);
            $table->string('dni', 8);
            $table->string('codigo', 13)->nullable();
            $table->string('email', 100);
            $table->string('numero_celular', 20);
            $table->string('cargo', 45);
            $table->string('password', 100);
            $table->tinyInteger('es_admin');
            $table->tinyInteger('estado');
            $table->timestamps();
            $table->softDeletes();
        });
        //Foreign Keys
        Schema::table('parameter', function(Blueprint $table){
            $table->foreignId('unit_id')->constrained('unit_measurement');
        });
        Schema::table('phase', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained('project');
        });
        Schema::table('project', function (Blueprint $table) {
            $table->foreignId('empresa_ejecutora_id')->constrained('company');
            $table->foreignId('responsable_propio_id')->constrained('user');
            $table->foreignId('responsable_externo_id')->constrained('user');
        });
        Schema::table('user', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained('company');
        });
        Schema::enableForeignKeyConstraints();
        print("Finalizado!\n");
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('company');
        Schema::dropIfExists('phase');
        Schema::dropIfExists('project');
        Schema::dropIfExists('user');
        Schema::enableForeignKeyConstraints();
    }
}
