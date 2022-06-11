<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringPointParameterRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_point_parameter_register', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')->constrained('record');
            $table->foreignId('mpp_id')->constrained('monitoring_point_parameter');
            $table->string('valor_cualitativo')->nullable();
            $table->decimal('valor_cuantitativo', 12, 3)->nullable();
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
        Schema::dropIfExists('monitoring_point_parameter_register');
    }
}
