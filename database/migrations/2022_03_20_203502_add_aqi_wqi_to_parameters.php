<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAqiWqiToParameters extends Migration
{
    public function up()
    {
        Schema::table('parameter', function (Blueprint $table) {
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
            $table->dropColumn('aqi_1');
            $table->dropColumn('aqi_2');
            $table->dropColumn('aqi_3');
            $table->dropColumn('aqi_4');
            $table->dropColumn('aqi_5');
            $table->dropColumn('valor_ideal');
            $table->dropColumn('usa_estandar');
            $table->dropColumn('usa_aqi');
            $table->dropColumn('usa_wqi');
            $table->dropColumn('no_aplica');
        });
    }
}
