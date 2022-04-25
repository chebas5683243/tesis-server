<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordDateToTableRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('record', function (Blueprint $table) {
            $table->datetime('fecha_registro')->after('monitoring_point_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('record', function (Blueprint $table) {
            $table->dropColumn('fecha_registro');
        });
    }
}
