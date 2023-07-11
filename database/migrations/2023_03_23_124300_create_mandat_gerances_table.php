<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandatGerancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandat_gerances', function (Blueprint $table) {
            $table->id();
            $table->string('mandat_id')->unique(); 
            $table->string('proprio');
            $table->string('bien'); 
            $table->string('agence'); 
            $table->string('pers'); 
            $table->string('mandat_duree')->nullable(); 
            $table->string('mandat_position')->nullable();
            $table->string('mandat_preavis_mandataire'); 
            $table->string('mandat_preavis_proprio'); 
            $table->string('mandat_honoraire_gestion');  
            $table->date('mandat_date_debut'); 
            $table->date('mandat_date_fin'); 
            $table->string('mandat_user'); 
            $table->text('mandat_fichiers')->nullable(); 
            $table->string('mandat_reserve_1')->nullable();  
            $table->string('mandat_reserve_2')->nullable(); 
            $table->string('mandat_reserve_3')->nullable(); 
            $table->string('mandat_reserve_4')->nullable(); 
            $table->string('mandat_reserve_5')->nullable(); 
            $table->timestamps();

            $table->foreign('proprio')->references('proprio_id')->on('proprietaires');
            $table->foreign('bien')->references('bien_id')->on('biens');
            $table->foreign('agence')->references('agence_id')->on('agences');
            $table->foreign('pers')->references('pers_id')->on('personnels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mandat_gerances');
    }
}
