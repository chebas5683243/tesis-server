<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigation', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 13)->nullable();
            $table->date('fecha_inicio_investigacion');
            $table->date('fecha_fin_investigacion')->nullable();
            $table->text('detalle_evento');
            $table->text('detalle_pre_evento');
            $table->text('detalle_post_evento');
            $table->date('fecha_incidente');
            $table->time('hora_incidente');
            $table->string('localidad', 100);
            $table->string('zona_sector', 100);
            $table->string('distrito', 100);
            $table->string('provincia', 100);
            $table->string('departamento', 100);
            $table->decimal('coordenada_este', 12, 5);
            $table->decimal('coordenada_norte', 12, 5);
            $table->text('detalle_ubicacion');
            $table->tinyInteger('estado');
            $table->foreignId('project_id')->constrained('project');
            $table->foreignId('reportante_id')->constrained('user');
            $table->foreignId('incident_type_id')->constrained('incident_type');
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
        Schema::dropIfExists('investigation');
    }
}
