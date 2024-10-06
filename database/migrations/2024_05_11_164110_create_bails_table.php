<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bails', function (Blueprint $table) {
            $table->id();
            $table->string('bail_id')->unique(); 
            $table->string('bail_bien'); 
            $table->text('bail_local')->nullable();
            $table->string('bail_proprio');
            $table->string('bail_type');
            $table->boolean('bail_etat');
            $table->string('bail_locataire');
            $table->string('bail_duree_contrat');
            $table->double('bail_montant_ht');
            $table->double('bail_caution_mnt_ht');
            $table->double('bail_frais_retard'); // Pénalité
            $table->date('bail_date_debut'); 
            $table->date('bail_date_fin'); 
            $table->string('bail_depot_garantie')->nullable();
            $table->string('bail_garant')->nullable();
            $table->string('bail_user'); 
            $table->string('bail_fichiers');
            $table->string('bail_reserve_1')->nullable();  
            $table->string('bail_reserve_2')->nullable(); 
            $table->string('bail_reserve_3')->nullable(); 
            $table->string('bail_reserve_4')->nullable(); 
            $table->string('bail_reserve_5')->nullable(); 
            
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
        Schema::dropIfExists('bails');
    }
}
