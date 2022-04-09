<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImmediateCauseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('immediate_cause', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_causa', 100);
            $table->text('descripcion');
            $table->foreignId('incident_id')->nullable()->constrained('incident');
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
        Schema::dropIfExists('immediate_cause');
    }
}
