<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->string('bien_id')->unique(); 
            $table->string('bien_proprio'); // id propriétaire
            $table->string('bien_nom')->nullable(); // nom immeuble
            $table->string('bien_adresse')->nullable();
            $table->string('bien_etage')->nullable();
            $table->string('bien_numero')->nullable();
            $table->string('bien_ville')->nullable();
            $table->string('bien_pays')->nullable();
            $table->text('bien_photos')->nullable();
            $table->string('bien_superficie')->nullable();
            $table->string('bien_annee_construction')->nullable();
            $table->string('bien_description')->nullable();
            $table->text('bien_local')->nullable(); // ID des local
            $table->string('user');
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
        Schema::dropIfExists('biens');
    }
}
