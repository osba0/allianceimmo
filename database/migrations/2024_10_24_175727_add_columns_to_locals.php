<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToLocals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locals', function (Blueprint $table) {
            $table->string('local_nature_local')->nullable();
            $table->integer('local_nbre_toilette')->nullable();
            $table->integer('local_nbre_chambre')->nullable();
            $table->integer('local_nbre_salle_bain')->nullable();
            $table->integer('local_nbre_cuisine')->nullable();
            $table->integer('local_nbre_piscine')->nullable();
            $table->integer('local_tom')->nullable();
            $table->integer('local_tva')->nullable();
            $table->integer('local_tlv')->nullable();
            $table->double('local_timbre_principal')->nullable();
            $table->double('local_timbre')->nullable();
            $table->double('local_eau_forfait')->nullable();
            $table->double('local_reserve_1')->nullable();
            $table->double('local_reserve_2')->nullable();
            $table->double('local_reserve_3')->nullable();
            $table->double('local_reserve_4')->nullable();
            $table->double('local_reserve_5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locals', function (Blueprint $table) {
            $table->dropColumn('local_nature_local');
            $table->dropColumn('local_nbre_toilette');
            $table->dropColumn('local_nbre_chambre');
            $table->dropColumn('local_nbre_salle_bain');
            $table->dropColumn('local_nbre_cuisine');
            $table->dropColumn('local_nbre_piscine');
            $table->dropColumn('local_tom');
            $table->dropColumn('local_tva');
            $table->dropColumn('local_tlv');
            $table->dropColumn('local_timbre_principal');
            $table->dropColumn('local_timbre');
            $table->dropColumn('local_eau_forfait');
            $table->dropColumn('local_reserve_1');
            $table->dropColumn('local_reserve_2');
            $table->dropColumn('local_reserve_3');
            $table->dropColumn('local_reserve_4');
            $table->dropColumn('local_reserve_5');
        });
    }
}
