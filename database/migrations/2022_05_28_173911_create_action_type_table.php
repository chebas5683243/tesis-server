<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_type', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion');
        });

        Schema::table('action', function (Blueprint $table) {
            $table->dropColumn('tipo_accion');
            $table->foreignId('action_type_id')->constrained('action_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_type');

        Schema::table('action', function (Blueprint $table) {
            $table->dropColumn('action_type_id');
            $table->string('tipo_accion', 100);
        });
    }
}
