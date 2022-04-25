<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValorEstandarPermisibleToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parameter', function (Blueprint $table) {
            $table->float('valor_estandar_permisible')->nullable()->after('valor_ideal');
        });

        Schema::table('monitoring_point_parameter', function (Blueprint $table) {
            $table->float('valor_estandar_permisible')->nullable()->after('valor_ideal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parameter', function (Blueprint $table) {
            $table->dropColumn('valor_estandar_permisible');
        });

        Schema::table('monitoring_point_parameter', function (Blueprint $table) {
            $table->dropColumn('valor_estandar_permisible');
        });
    }
}
