<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImmediateActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('immediate_action', function (Blueprint $table) {
            $table->id();
            $table->string('responsable', 200);
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
        Schema::dropIfExists('immediate_action');
    }
}
