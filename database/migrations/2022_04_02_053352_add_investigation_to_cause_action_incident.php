<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvestigationToCauseActionIncident extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident', function (Blueprint $table) {
            $table->foreignId('investigation_id')->nullable()->constrained('investigation');
        });

        Schema::table('immediate_cause', function (Blueprint $table) {
            $table->foreignId('investigation_id')->nullable()->constrained('investigation');
        });

        Schema::table('immediate_action', function (Blueprint $table) {
            $table->foreignId('investigation_id')->nullable()->constrained('investigation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cause_action_incident', function (Blueprint $table) {
            //
        });
    }
}
