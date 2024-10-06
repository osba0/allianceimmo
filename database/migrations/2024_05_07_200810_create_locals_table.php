<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locals', function (Blueprint $table) {
            $table->id();
            $table->string('local_id')->unique(); 
            $table->string('bien_id'); 
            $table->string('local_type_local')->nullable();
            $table->string('local_type_location')->nullable();
            $table->double('local_prix_loyer')->nullable();
            $table->double('local_montant_charge')->nullable();
            $table->string('local_superficie')->nullable();
            $table->string('local_nombre_piece')->nullable();
            $table->string('local_salle_bain')->nullable();
            $table->string('local_description')->nullable();
            $table->string('local_annee_construction')->nullable();
            $table->boolean('local_disponible'); // new 11/07/2024
            $table->text('local_photos')->nullable();
            $table->string('user');
            $table->timestamps();

            $table->foreign('bien_id')->references('bien_id')->on('biens');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locals');
    }
}
