<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCauseTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cause_type', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion');
        });

        Schema::table('immediate_cause', function (Blueprint $table) {
            $table->dropColumn('tipo_causa');
            $table->foreignId('cause_type_id')->constrained('cause_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cause_type');
        
        Schema::table('immediate_cause', function (Blueprint $table) {
            $table->string('tipo_causa', 100);
        });
    }
}
