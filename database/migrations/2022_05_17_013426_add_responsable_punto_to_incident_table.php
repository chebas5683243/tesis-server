<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResponsablePuntoToIncidentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident', function (Blueprint $table) {
            $table->dropForeign(['reportante_id']);
            $table->dropColumn('reportante_id');
            $table->foreignId('responsable_propio_id')->constrained('user');
            $table->foreignId('responsable_externo_id')->constrained('user');
            $table->foreignId('monitoring_point_id')->nullable()->constrained('monitoring_point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident', function (Blueprint $table) {
            $table->foreignId('reportante_id')->constrained('user');
            $table->dropColumn('responsable_propio_id');
            $table->dropColumn('responsable_externo_id');
            $table->dropColumn('monitoring_point_id');
        });
    }
}
